<?php

use Illuminate\Support\Facades\Event;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\Timeline\Listeners\RemoveRecordFromTimeline;

it('has a listener prepared to handle it', function () {
    Event::fake();

    Event::assertListening(
        TimelineableRecordDeleted::class,
        RemoveRecordFromTimeline::class
    );
});
