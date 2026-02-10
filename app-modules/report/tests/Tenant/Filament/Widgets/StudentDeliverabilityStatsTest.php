<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Filament\Widgets\StudentDeliverabilityStats;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\EmailAddressOptInOptOut;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Number;

it('returns correct percentages of students with Email missing, Email unhealthy, Phone unhealthy, and Phone missing within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $student1 = Student::factory()->state(['created_at_source' => $startDate])->create();
    $student1->primaryEmailAddress()->delete();
    $student1->update(['primary_email_id' => null]);
    $student1->primaryPhoneNumber()->delete();
    $student1->update(['primary_phone_id' => null]);

    $student2 = Student::factory()->state(['created_at_source' => $endDate])->create();

    $student3 = Student::factory()->state(['created_at_source' => $startDate])->create();
    BouncedEmailAddress::factory()->create(['address' => $student3->primaryEmailAddress->address]);
    $student3->primaryPhoneNumber->update(['can_receive_sms' => false]);

    $student4 = Student::factory()->state(['created_at_source' => $endDate])->create();
    EmailAddressOptInOptOut::factory()->create([
        'address' => $student4->primaryEmailAddress->address,
        'status' => EmailAddressOptInOptOutStatus::OptedOut,
    ]);
    SmsOptOutPhoneNumber::factory()->create(['number' => $student4->primaryPhoneNumber->number]);

    $student5 = Student::factory()->state(['created_at_source' => $endDate])->create();
    EmailAddressOptInOptOut::factory()->create([
        'address' => $student5->primaryEmailAddress->address,
        'status' => EmailAddressOptInOptOutStatus::OptedIn,
    ]);

    $totalStudents = Student::query()
        ->whereBetween('created_at_source', [$startDate, $endDate])
        ->count();
    $emailMissingCount = 1;
    $emailUnhealthyCount = 3;
    $phoneMissingCount = 1;
    $phoneUnhealthyCount = 3;

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toBe(Number::format(($emailMissingCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[1]->getValue())->toBe(Number::format(($emailUnhealthyCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[2]->getValue())->toBe(Number::format(($phoneMissingCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[3]->getValue())->toBe(Number::format(($phoneUnhealthyCount / $totalStudents) * 100, 2) . '%');
});

it('returns correct percentages of students Email missing, Email unhealthy, Phone unhealthy, and Phone missing based on group filters', function () {
    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $student1 = Student::factory()->state(['last' => 'John'])->create();
    $student1->primaryEmailAddress()->delete();
    $student1->update(['primary_email_id' => null]);
    $student1->primaryPhoneNumber()->delete();
    $student1->update(['primary_phone_id' => null]);

    $student2 = Student::factory()->state(['last' => 'John'])->create();
    BouncedEmailAddress::factory()->create(['address' => $student2->primaryEmailAddress->address]);
    $student2->primaryPhoneNumber->update(['can_receive_sms' => false]);

    $student3 = Student::factory()->state(['last' => 'John'])->create();

    $student4 = Student::factory()->state(['last' => 'Doe'])->create();
    $student4->primaryEmailAddress()->delete();
    $student4->update(['primary_email_id' => null]);
    $student4->primaryPhoneNumber()->delete();
    $student4->update(['primary_phone_id' => null]);

    $totalStudentsWithFilter = 3;
    $emailMissingCountWithFilter = 1;
    $emailUnhealthyCountWithFilter = 2;
    $phoneMissingCountWithFilter = 1;
    $phoneUnhealthyCountWithFilter = 2;

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toBe(Number::format(($emailMissingCountWithFilter / $totalStudentsWithFilter) * 100, 2) . '%')
        ->and($stats[1]->getValue())->toBe(Number::format(($emailUnhealthyCountWithFilter / $totalStudentsWithFilter) * 100, 2) . '%')
        ->and($stats[2]->getValue())->toBe(Number::format(($phoneMissingCountWithFilter / $totalStudentsWithFilter) * 100, 2) . '%')
        ->and($stats[3]->getValue())->toBe(Number::format(($phoneUnhealthyCountWithFilter / $totalStudentsWithFilter) * 100, 2) . '%');

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [];

    $stats = $widget->getStats();

    $totalStudentsWithoutFilter = 4;
    $emailMissingCountWithoutFilter = 2;
    $emailUnhealthyCountWithoutFilter = 3;
    $phoneMissingCountWithoutFilter = 2;
    $phoneUnhealthyCountWithoutFilter = 3;

    expect($stats[0]->getValue())->toBe(Number::format(($emailMissingCountWithoutFilter / $totalStudentsWithoutFilter) * 100, 2) . '%')
        ->and($stats[1]->getValue())->toBe(Number::format(($emailUnhealthyCountWithoutFilter / $totalStudentsWithoutFilter) * 100, 2) . '%')
        ->and($stats[2]->getValue())->toBe(Number::format(($phoneMissingCountWithoutFilter / $totalStudentsWithoutFilter) * 100, 2) . '%')
        ->and($stats[3]->getValue())->toBe(Number::format(($phoneUnhealthyCountWithoutFilter / $totalStudentsWithoutFilter) * 100, 2) . '%');
});

it('handles zero students in date range without division errors', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    // Create students outside the date range
    Student::factory()->state(['created_at_source' => now()->subDays(20)])->create();
    Student::factory()->state(['created_at_source' => now()])->create();

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    // Should handle zero students gracefully (likely showing 0% or N/A)
    expect($stats[0]->getValue())->toBeString()
        ->and($stats[1]->getValue())->toBeString()
        ->and($stats[2]->getValue())->toBeString()
        ->and($stats[3]->getValue())->toBeString();
});

it('shows 0% for all metrics when all students are healthy', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    // Create healthy students with valid email and phone
    Student::factory()->state(['created_at_source' => $startDate])->create();
    Student::factory()->state(['created_at_source' => $endDate])->create();
    Student::factory()->state(['created_at_source' => now()->subDays(7)])->create();

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toBe('0.00%')
        ->and($stats[1]->getValue())->toBe('0.00%')
        ->and($stats[2]->getValue())->toBe('0.00%')
        ->and($stats[3]->getValue())->toBe('0.00%');
});

it('shows 100% for all metrics when all students are completely unhealthy', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    // Create students with missing email and phone
    $student1 = Student::factory()->state(['created_at_source' => $startDate])->create();
    $student1->primaryEmailAddress()->delete();
    $student1->update(['primary_email_id' => null]);
    $student1->primaryPhoneNumber()->delete();
    $student1->update(['primary_phone_id' => null]);

    $student2 = Student::factory()->state(['created_at_source' => $endDate])->create();
    $student2->primaryEmailAddress()->delete();
    $student2->update(['primary_email_id' => null]);
    $student2->primaryPhoneNumber()->delete();
    $student2->update(['primary_phone_id' => null]);

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toBe('100.00%')
        ->and($stats[1]->getValue())->toBe('100.00%')
        ->and($stats[2]->getValue())->toBe('100.00%')
        ->and($stats[3]->getValue())->toBe('100.00%');
});

it('includes students created exactly on start and end dates', function () {
    $startDate = now()->subDays(10)->startOfDay();
    $endDate = now()->subDays(5)->endOfDay();

    // Create students on exact boundaries
    $studentOnStart = Student::factory()->state(['created_at_source' => $startDate])->create();
    $studentOnStart->primaryEmailAddress()->delete();
    $studentOnStart->update(['primary_email_id' => null]);

    $studentOnEnd = Student::factory()->state(['created_at_source' => $endDate])->create();
    $studentOnEnd->primaryPhoneNumber()->delete();
    $studentOnEnd->update(['primary_phone_id' => null]);

    $studentInMiddle = Student::factory()->state(['created_at_source' => now()->subDays(7)])->create();

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    // Should include all 3 students
    $totalStudents = 3;
    $emailMissingCount = 1;
    $phoneMissingCount = 1;

    expect($stats[0]->getValue())->toBe(Number::format(($emailMissingCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[2]->getValue())->toBe(Number::format(($phoneMissingCount / $totalStudents) * 100, 2) . '%');
});

it('correctly excludes healthy students with opted-in emails and valid phones from unhealthy counts', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    // Create a fully healthy student with explicit opt-in
    $healthyStudent = Student::factory()->state(['created_at_source' => $startDate])->create();
    EmailAddressOptInOptOut::factory()->create([
        'address' => $healthyStudent->primaryEmailAddress->address,
        'status' => EmailAddressOptInOptOutStatus::OptedIn,
    ]);

    // Create another healthy student without any opt-in/opt-out record
    $normalStudent = Student::factory()->state(['created_at_source' => $endDate])->create();

    // Create an unhealthy student for comparison
    $unhealthyStudent = Student::factory()->state(['created_at_source' => now()->subDays(7)])->create();
    $unhealthyStudent->primaryEmailAddress()->delete();
    $unhealthyStudent->update(['primary_email_id' => null]);
    $unhealthyStudent->primaryPhoneNumber()->delete();
    $unhealthyStudent->update(['primary_phone_id' => null]);

    $widget = new StudentDeliverabilityStats();
    $widget->cacheTag = 'report-student-deliverability';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    $totalStudents = 3;
    $emailMissingCount = 1; // Only unhealthyStudent
    $emailUnhealthyCount = 1; // Only unhealthyStudent
    $phoneMissingCount = 1; // Only unhealthyStudent
    $phoneUnhealthyCount = 1; // Only unhealthyStudent

    expect($stats[0]->getValue())->toBe(Number::format(($emailMissingCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[1]->getValue())->toBe(Number::format(($emailUnhealthyCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[2]->getValue())->toBe(Number::format(($phoneMissingCount / $totalStudents) * 100, 2) . '%')
        ->and($stats[3]->getValue())->toBe(Number::format(($phoneUnhealthyCount / $totalStudents) * 100, 2) . '%');
});
