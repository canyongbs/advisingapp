<?php

use Illuminate\Support\Carbon;
use AdvisingApp\Report\Models\TrackedEvent;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Report\Models\TrackedEventCount;

it('creates the proper TrackedEvent record', function () {
    expect(TrackedEvent::count())
        ->toBe(0);

    $type = fake()->randomElement(TrackedEventType::cases());
    $occurredAt = Carbon::parse(fake()->dateTime());

    (new RecordTrackedEvent(
        type: $type,
        occurredAt: $occurredAt,
    ))
        ->handle();

    $trackedEvents = TrackedEvent::all();

    expect($trackedEvents->count())
        ->toBe(1)
        ->and($trackedEvents->first())
        ->type->toEqual($type)
        ->occurred_at->toEqual($occurredAt);
});

it('creates the proper TrackedEventCount record if one does not already exist', function () {
    expect(TrackedEventCount::count())
        ->toBe(0);

    $type = fake()->randomElement(TrackedEventType::cases());
    $occurredAt = Carbon::parse(fake()->dateTime());

    (new RecordTrackedEvent(
        type: $type,
        occurredAt: $occurredAt,
    ))
        ->handle();

    $trackedEventsCounts = TrackedEventCount::all();

    expect($trackedEventsCounts->count())
        ->toBe(1)
        ->and($trackedEventsCounts->first())
        ->type->toEqual($type)
        ->count->toBe(1)
        ->last_occurred_at->toEqual($occurredAt);
});

it('updates the proper TrackedEventCount record if one already exists', function () {
    /** @var TrackedEventCount $originalTrackedEventCount */
    $originalTrackedEventCount = TrackedEventCount::factory()->create();

    expect(TrackedEventCount::count())
        ->toBe(1);

    $occurredAt = Carbon::parse(fake()->dateTimeBetween(
        startDate: $originalTrackedEventCount->last_occurred_at,
        endDate: $originalTrackedEventCount->last_occurred_at->clone()->addYear()
    ));

    (new RecordTrackedEvent(
        type: $originalTrackedEventCount->type,
        occurredAt: $occurredAt,
    ))
        ->handle();

    expect(TrackedEventCount::count())
        ->toBe(1);

    expect($originalTrackedEventCount->fresh())
        ->type->toEqual($originalTrackedEventCount->type)
        ->count->toBe($originalTrackedEventCount->count + 1)
        ->last_occurred_at->toEqual($occurredAt);
});

it('does not update the TrackedEventCount record last_occurate_at date if the event was before the current set date', function () {
    /** @var TrackedEventCount $originalTrackedEventCount */
    $originalTrackedEventCount = TrackedEventCount::factory()->create();

    expect(TrackedEventCount::count())
        ->toBe(1);

    $occurredAt = Carbon::parse(fake()->dateTime(max: $originalTrackedEventCount->last_occurred_at));

    (new RecordTrackedEvent(
        type: $originalTrackedEventCount->type,
        occurredAt: $occurredAt,
    ))
        ->handle();

    expect(TrackedEventCount::count())
        ->toBe(1);

    expect($originalTrackedEventCount->fresh())
        ->type->toEqual($originalTrackedEventCount->type)
        ->count->toBe($originalTrackedEventCount->count + 1)
        ->last_occurred_at->toEqual($originalTrackedEventCount->last_occurred_at)
        ->not->toEqual($occurredAt);
});
