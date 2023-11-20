<?php

use Illuminate\Support\Facades\Event;
use Assist\Timeline\Listeners\AddRecordToTimeline;
use Assist\Timeline\Events\TimelineableRecordCreated;

it('has a listener prepared to handle it', function () {
    Event::fake();

    Event::assertListening(
        TimelineableRecordCreated::class,
        AddRecordToTimeline::class
    );
});
