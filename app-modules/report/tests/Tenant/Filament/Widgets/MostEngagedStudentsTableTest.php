<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Report\Filament\Widgets\MostEngagedStudentsTable;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Carbon\Carbon;
use Filament\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

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
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $student1,
            $student2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$student3]));
});

it('returns top engaged students engagements based on group filter', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

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

    $student1 = Student::factory()->state(['created_at' => $startDate, 'last' => 'John'])->create();
    $student2 = Student::factory()->state(['created_at' => $endDate, 'last' => 'Doe'])->create();

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

    $filters = [
        'populationGroup' => $group->getKey(),
    ];

    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-student-engagement',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $student1,
        ]))
        ->assertCanNotSeeTableRecords(collect([$student2]));

    // without filter
    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-student-engagement',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $student1,
            $student2,
        ]));
});

it('has an export action', function () {
    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-student-messages-overview',
        'filters' => [],
    ])->assertTableActionExists(ExportAction::class);
});

it('can start an export, sending a notification', function () {
    asSuperAdmin();
    Storage::fake('s3');
    $count = random_int(1, 5);
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();
    $student3 = Student::factory()->create();

    Engagement::factory()->count($count)->state([
        'recipient_id' => $student1->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    Engagement::factory()->count($count)->state([
        'recipient_id' => $student2->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    Engagement::factory()->count($count)->state([
        'recipient_id' => $student3->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
    ])->create();

    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-student-engagement',
        'filters' => [],
    ])
        ->callTableAction(ExportAction::class)
        ->assertNotified()
        ->assertHasNoTableActionErrors();
});

it('does not list archived students', function () {
    $active = Student::factory()->count(2)->create();
    $archived = Student::factory()->create();
    $archived->archive();

    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-students',
        'pageFilters' => [],
    ])
        ->assertCanSeeTableRecords($active)
        ->assertCanNotSeeTableRecords(collect([$archived]));
});

// Archiving is not retroactive: a student engaged in March and archived in September was
// genuinely part of the student body in March, so a report looking at March still lists them.
it('lists a student archived after the reported period without linking to them', function () {
    $active = Student::factory()->create();

    $archived = Student::factory()->create();
    $archived->forceFill(['archived_at' => Carbon::parse('2026-09-15')])->save();

    Engagement::factory()->state([
        'recipient_id' => $active->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => Carbon::parse('2026-03-10'),
    ])->create();

    Engagement::factory()->state([
        'recipient_id' => $archived->sisid,
        'recipient_type' => (new Student())->getMorphClass(),
        'channel' => NotificationChannel::Email,
        'created_at' => Carbon::parse('2026-03-10'),
    ])->create();

    livewire(MostEngagedStudentsTable::class, [
        'cacheTag' => 'report-students',
        'pageFilters' => [
            'startDate' => '2026-03-01',
            'endDate' => '2026-03-31',
        ],
    ])
        ->assertCanSeeTableRecords(collect([$active, $archived]))
        ->assertTableColumnExists(
            'full_name',
            fn (TextColumn $column): bool => $column->getUrl() === StudentResource::getUrl('view', ['record' => $active]),
            $active,
        )
        // Listed for the period, but their page no longer resolves, so the name is not a link.
        // The record is read back through this table's own query, so this also proves the
        // explicit `select()` loads `archived_at` — without it the link would come back.
        ->assertTableColumnExists(
            'full_name',
            fn (TextColumn $column): bool => $column->getUrl() === null,
            $archived,
        );
});
