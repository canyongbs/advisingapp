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
