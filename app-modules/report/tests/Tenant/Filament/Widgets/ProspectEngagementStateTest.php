<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectEngagementState;
use App\Models\User;

it('returns correct counts of prospects, emails, texts, and staff based on the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospect1 = Prospect::factory()->state(['created_at' => $startDate])->create();
    $prospect2 = Prospect::factory()->state(['created_at' => $endDate])->create();

    $emailCount = 2;
    $textCount = 3;

    $user1 = User::factory()->create();
    Engagement::factory()->count($emailCount)->state([
        'user_id' => $user1->id,
        'recipient_id' => $prospect1->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    $user2 = User::factory()->create();
    Engagement::factory()->count($textCount)->state([
        'user_id' => $user2->id,
        'recipient_id' => $prospect2->id,
        'recipient_type' => (new Prospect())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widget = new ProspectEngagementState();
    $widget->cacheTag = 'report-prospect-engagement';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual(2)
        ->and($stats[1]->getValue())->toEqual($emailCount)
        ->and($stats[2]->getValue())->toEqual($textCount)
        ->and($stats[3]->getValue())->toEqual(2);
});
