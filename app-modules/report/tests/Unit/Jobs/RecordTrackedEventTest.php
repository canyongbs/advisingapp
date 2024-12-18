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
    TrackedEventCount::truncate();

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
    TrackedEventCount::truncate();

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
})->only();

it('does not update the TrackedEventCount record last_occurate_at date if the event was before the current set date', function () {
    TrackedEventCount::truncate();

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
