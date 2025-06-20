<?php

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\MostEngagedStudentsTable;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;

it('returns top engaged students based on engagements within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $student1 = Student::factory()->state(['created_at' => $startDate])->create();
    $student2 = Student::factory()->state(['created_at' => $endDate])->create();
    $student3 = Student::factory()->state(['created_at' => now()->subDays(20)])->create();

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

    Engagement::factory()->count(3)->state([
        'recipient_id' => $student3->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-student-engagement',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $student1,
            $student2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$student3]));
});
