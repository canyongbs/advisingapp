<?php

use AdvisingApp\Report\Filament\Widgets\StudentDeliverableTable;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;

it('it returns deliverability data only for students created within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $optOutStartDateStudents = Student::factory()
        ->count(2)
        ->create([
            'email_bounce' => false,
            'sms_opt_out' => false,
            'created_at_source' => $startDate,
        ]);

    $optOutEndDateStudents = Student::factory()
        ->count(2)
        ->create([
            'email_bounce' => false,
            'sms_opt_out' => false,
            'created_at_source' => $endDate,
        ]);

    $optInStartDateStudents = Student::factory()
        ->count(2)
        ->create([
            'email_bounce' => true,
            'sms_opt_out' => false,
            'created_at_source' => $startDate,
        ]);

    $optInEndDateStudents = Student::factory()
        ->count(2)
        ->create([
            'email_bounce' => false,
            'sms_opt_out' => true,
            'created_at_source' => $endDate,
        ]);

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords($optInStartDateStudents->merge($optInEndDateStudents))
        ->assertCanNotSeeTableRecords($optOutStartDateStudents->merge($optOutEndDateStudents));
});
