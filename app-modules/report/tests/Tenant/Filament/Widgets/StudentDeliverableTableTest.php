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
use AdvisingApp\Report\Filament\Widgets\StudentDeliverableTable;
use AdvisingApp\StudentDataModel\Models\BouncedEmailAddress;
use AdvisingApp\StudentDataModel\Models\SmsOptOutPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;

it('it returns deliverability data only for students created within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $optOutStartDateStudents = Student::factory()
        ->count(2)
        ->create([
            'created_at_source' => $startDate,
        ]);

    $optOutStartDateStudents->each(function (Student $student) {
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
    });
    $optOutEndDateStudents = Student::factory()
        ->count(2)
        ->create([
            'created_at_source' => $endDate,
        ]);

    $optInStartDateStudents = Student::factory()
        ->count(2)
        ->create([
            'created_at_source' => $startDate->addDay(),
        ]);

    $optInEndDateStudents = Student::factory()
        ->count(2)
        ->create([
            'created_at_source' => $endDate->subDay(),
        ]);

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords($optInStartDateStudents->merge($optInEndDateStudents))
        ->assertCanNotSeeTableRecords($optOutStartDateStudents->merge($optOutEndDateStudents));
});

it('it returns deliverability data only for students based on group filters', function () {
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

    $studentWithJoeName = Student::factory()
        ->count(2)
        ->create([
            'last' => 'John',
        ]);

    $studentWithDoeName = Student::factory()
        ->count(2)
        ->create([
            'last' => 'Doe',
        ]);

    $filters = [
        'populationGroup' => $group->getKey(),
    ];

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords($studentWithJoeName)
        ->assertCanNotSeeTableRecords($studentWithDoeName);

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords($studentWithJoeName->merge($studentWithDoeName));
});

it('can filter table based on email bounce status', function () {
    $unHealthyEmailsStudents = Student::factory()
        ->count(2)
        ->create();

    $unHealthyEmailsStudents->each(function (Student $student) {
        BouncedEmailAddress::factory()->create([
            'address' => $student->primaryEmailAddress->address,
        ]);
    });

    $healthyStudents = Student::factory()
        ->count(2)
        ->create();

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
    ])
        ->assertCanSeeTableRecords($healthyStudents->merge($unHealthyEmailsStudents));

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
    ])
        ->filterTable('email_status', 'unhealthy')
        ->assertCanSeeTableRecords($unHealthyEmailsStudents)
        ->assertCanNotSeeTableRecords($healthyStudents)
        ->filterTable('email_status', 'healthy')
        ->assertCanSeeTableRecords($healthyStudents)
        ->assertCanNotSeeTableRecords($unHealthyEmailsStudents);
});

it('can filter table based on phone sms opt-out status', function () {
    $unHealthyPhoneStudents = Student::factory()
        ->count(2)
        ->create();

    $unHealthyPhoneStudents->each(function (Student $student) {
        SmsOptOutPhoneNumber::factory()->create([
            'number' => $student->primaryPhoneNumber->number,
        ]);
    });

    $healthyStudents = Student::factory()
        ->count(2)
        ->create();

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
    ])
        ->assertCanSeeTableRecords($healthyStudents->merge($unHealthyPhoneStudents));

    livewire(StudentDeliverableTable::class, [
        'cacheTag' => 'report-student-deliverability',
    ])
        ->filterTable('phone_status', 'unhealthy')
        ->assertCanSeeTableRecords($unHealthyPhoneStudents)
        ->assertCanNotSeeTableRecords($healthyStudents)
        ->filterTable('phone_status', 'healthy')
        ->assertCanSeeTableRecords($healthyStudents)
        ->assertCanNotSeeTableRecords($unHealthyPhoneStudents);
});
