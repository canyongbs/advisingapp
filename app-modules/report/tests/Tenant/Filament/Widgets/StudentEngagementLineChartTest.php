<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\StudentEngagementLineChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('returns correct monthly email and sms engagement data for students within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $student1 = Student::factory()->state(['created_at_source' => $startDate])->create();
    $student2 = Student::factory()->state(['created_at_source' => $endDate])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $student1->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => $startDate,
    ])->create();

    Engagement::factory()->count(5)->state([
        'recipient_id' => $student2->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Sms,
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new StudentEngagementLineChart();
    $widgetInstance->cacheTag = 'report-student-engagement';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});
