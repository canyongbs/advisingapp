<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectEngagementLineChart;

it('returns correct monthly email and sms engagement data for prospects within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $prospect1 = Prospect::factory()->state(['created_at' => $startDate])->create();
    $prospect2 = Prospect::factory()->state(['created_at' => $endDate])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect1->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $prospect2->getKey(),
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new ProspectEngagementLineChart();
    $widgetInstance->cacheTag = 'report-prospect-engagement';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});
