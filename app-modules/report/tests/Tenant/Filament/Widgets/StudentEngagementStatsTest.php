<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\StudentEngagementStats;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

it('returns correct counts of students, emails, texts, and staff engagements within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $student1 = Student::factory()->state(['created_at_source' => $startDate])->create();
    $student2 = Student::factory()->state(['created_at_source' => $endDate])->create();

    $emailCount = 2;
    $textCount = 3;

    $user1 = User::factory()->create();
    Engagement::factory()->count($emailCount)->state([
        'user_id' => $user1->getKey(),
        'recipient_id' => $student1->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    $user2 = User::factory()->create();
    Engagement::factory()->count($textCount)->state([
        'user_id' => $user2->getKey(),
        'recipient_id' => $student2->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widget = new StudentEngagementStats();
    $widget->cacheTag = 'report-student-engagement';
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
