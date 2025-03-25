<?php

use AdvisingApp\Report\Filament\Widgets\StudentDeliverableTable;
use AdvisingApp\Report\Filament\Widgets\StudentEmailOptInOptOutPieChart;
use AdvisingApp\Report\Filament\Widgets\StudentSmsOptInOptOutPieChart;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;

it('ensures the pie chart reflects the correct student email opt-in and opt-out stats', function () {
    $pieChart = new StudentEmailOptInOptOutPieChart();
    $pieChart->cacheTag = 'student_email_opt_in_out';

    $emailOptOutStudents = Student::factory()->count(10)->create([
        'email_bounce' => true,
    ]);
    $emailOptInStudents = Student::factory()->count(4)->create([
        'email_bounce' => false,
    ]);

    $totalstudents = Student::count();

    $optOutStudents = number_format($emailOptOutStudents->count() / $totalstudents * 100, 2);
    $optInStudents = number_format($emailOptInStudents->count() / $totalstudents * 100, 2);

    $stats = $pieChart->getData()['datasets'][0]['data'];

    expect($optOutStudents)->toEqual($stats[1]);
    expect($optInStudents)->toEqual($stats[0]);
});

it('ensures the pie chart reflects the correct student text opt-in and opt-out stats', function () {
    $pieChart = new StudentSmsOptInOptOutPieChart();
    $pieChart->cacheTag = 'student_text_opt_in_out';

    $textOptOutStudents = Student::factory()->count(5)->create([
        'sms_opt_out' => true,
    ]);
    $textOptInStudents = Student::factory()->count(3)->create([
        'sms_opt_out' => false,
    ]);

    $stats = $pieChart->getData()['datasets'][0]['data'];

    expect($textOptInStudents)->count()->toEqual($stats[0]);
    expect($textOptOutStudents)->count()->toEqual($stats[1]);
});

it('ensure displays correct student records based on email and sms preferences', function () {
    $emailBouncedStudents = Student::factory()->count(3)->create([
        'email_bounce' => true,
        'sms_opt_out' => false,
    ]);

    $smsOptOutStudents = Student::factory()->count(2)->create([
        'email_bounce' => false,
        'sms_opt_out' => true,
    ]);

    $fullyOptedInStudents = Student::factory()->count(2)->create([
        'email_bounce' => false,
        'sms_opt_out' => false,
    ]);

    livewire(StudentDeliverableTable::class, ['cacheTag' => 'student_communication_preferences'])
        ->assertCanSeeTableRecords($emailBouncedStudents->merge($smsOptOutStudents))
        ->assertCanNotSeeTableRecords($fullyOptedInStudents);
});
