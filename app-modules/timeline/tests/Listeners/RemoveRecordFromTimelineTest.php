<?php

use Assist\Timeline\Models\Timeline;
use Illuminate\Support\Facades\Cache;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Listeners\AddRecordToTimeline;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\Timeline\Listeners\RemoveRecordFromTimeline;

it('busts the timeline cache for the associated educatable', function () {
    // Given we have a timelineable record, like an EngagementResponse
    $initialResponse = EngagementResponse::factory()->create();

    // And our educatable has a timeline synced cache key
    cache()->put(
        "timeline.synced.{$initialResponse->sender->getMorphClass()}.{$initialResponse->sender->getKey()}",
        now(),
        now()->addMinutes(60)
    );

    // When we create another timelineable record
    $subsequentResponse = $initialResponse->sender->engagementResponses()->createQuietly([
        'content' => 'This is a test response',
        'sent_at' => now(),
    ]);

    $event = new TimelineableRecordDeleted($subsequentResponse->sender, $subsequentResponse);
    $listener = app(RemoveRecordFromTimeline::class);

    $listener->handle($event);

    // The cache key for the educatable should be busted
    expect(Cache::has(
        "timeline.synced.{$initialResponse->sender->getMorphClass()}.{$initialResponse->sender->getKey()}"
    ))->toBeFalse();
});

it('should remove the specified record to the timeline', function () {
    expect(Timeline::count())->toBe(0);

    $response = EngagementResponse::factory()->createQuietly();

    $event = new TimelineableRecordCreated($response->sender, $response);
    $listener = app(AddRecordToTimeline::class);

    $listener->handle($event);

    expect(Timeline::count())->toBe(1);

    $event = new TimelineableRecordDeleted($response->sender, $response);
    $listener = app(RemoveRecordFromTimeline::class);

    $listener->handle($event);

    expect(Timeline::count())->toBe(0);
});
