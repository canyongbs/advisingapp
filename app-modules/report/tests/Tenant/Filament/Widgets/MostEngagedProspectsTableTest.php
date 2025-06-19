<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\MostEngagedProspectsTable;

use function Pest\Livewire\livewire;

it('returns top engaged prospects based on engagements within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospect1 = Prospect::factory()->state(['created_at' => $startDate])->create();
    $prospect2 = Prospect::factory()->state(['created_at' => $endDate])->create();
    $prospect3 = Prospect::factory()->state(['created_at' => now()->subDays(20)])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect1->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect2->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    Engagement::factory()->count(3)->state([
        'recipient_id' => $prospect3->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostEngagedProspectsTable::class, [
        'cacheTag' => 'report-prospect-engagement',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $prospect1,
            $prospect2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$prospect3]));
});
