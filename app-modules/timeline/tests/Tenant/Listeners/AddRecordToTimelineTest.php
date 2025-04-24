<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Listeners\AddRecordToTimeline;
use AdvisingApp\Timeline\Models\Timeline;
use Illuminate\Support\Facades\Cache;

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
        'type' => EngagementResponseType::Sms,
        'content' => 'This is a test response',
        'sent_at' => now(),
        'status' => EngagementResponseStatus::New,
    ]);

    $event = new TimelineableRecordCreated($subsequentResponse->sender, $subsequentResponse);
    $listener = app(AddRecordToTimeline::class);

    $listener->handle($event);

    // The cache key for the educatable should be busted
    expect(Cache::has(
        "timeline.synced.{$initialResponse->sender->getMorphClass()}.{$initialResponse->sender->getKey()}"
    ))->toBeFalse();
});

it('should add the specified record to the timeline', function () {
    // Given we have no records in the timeline
    expect(Timeline::count())->toBe(0);

    // And we add a record that is timelineable
    $response = EngagementResponse::factory()->createQuietly();

    $event = new TimelineableRecordCreated($response->sender, $response);
    $listener = app(AddRecordToTimeline::class);

    $listener->handle($event);

    // Our timeline should reflect that
    expect(Timeline::count())->toBe(1);
    expect(Timeline::first()->timelineable_id)->toBe($response->getKey());
    expect(Timeline::first()->timelineable_type)->toBe($response->getMorphClass());
    expect(Timeline::first()->entity_id)->toBe($response->sender->getKey());
    expect(Timeline::first()->entity_type)->toBe($response->sender->getMorphClass());
});
