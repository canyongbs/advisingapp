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

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Models\User;
use Filament\Actions\CreateAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can filter students by first generation', function () {
    Student::truncate();

    asSuperAdmin();

    $studentsWithFirstGen = Student::factory()
        ->state([
            'firstgen' => true,
        ])->count(5)->create();

    $studentsWithoutFirstGen = Student::factory()
        ->state([
            'firstgen' => false,
        ])->count(5)->create();

    livewire(ListStudents::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($studentsWithFirstGen->merge($studentsWithoutFirstGen))
        ->filterTable('firstgen', true)
        ->assertCanSeeTableRecords($studentsWithFirstGen)
        ->assertCanNotSeeTableRecords($studentsWithoutFirstGen)
        ->filterTable('firstgen', false)
        ->assertCanSeeTableRecords($studentsWithoutFirstGen)
        ->assertCanNotSeeTableRecords($studentsWithFirstGen);
});

it('renders the CreateAction based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.create');

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionHidden(CreateAction::class);

    $studentSettings = app(ManageStudentConfigurationSettings::class);
    $studentSettings->is_enabled = true;
    $studentSettings->save();

    $user->revokePermissionTo('student.create');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionHidden(CreateAction::class);

    $user->givePermissionTo('student.create');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertActionVisible(CreateAction::class);
});

it('can filter students by alerts', function () {
    Student::truncate();

    asSuperAdmin();

    $activeStatusAlert = AlertStatus::factory()
        ->state([
            'name' => 'Active',
            'classification' => SystemAlertStatusClassification::Active,
        ])
        ->create();

    $inprogressStatusAlert = AlertStatus::factory()
        ->state([
            'name' => 'InProgress',
            'classification' => SystemAlertStatusClassification::Active,
        ])
        ->create();

    $studentWithStatusActive = Student::factory()->create();

    $studentWithStatusInprogress = Student::factory()->create();

    $activeAlerts = Alert::factory()
        ->count(3)
        ->for($studentWithStatusActive, 'concern')
        ->state([
            'status_id' => $activeStatusAlert->getKey(),
        ])
        ->create();

    $inProgressAlerts = Alert::factory()
        ->count(2)
        ->for($studentWithStatusInprogress, 'concern')
        ->state([
            'status_id' => $inprogressStatusAlert->getKey(),
        ])
        ->create();

    $studentsWithoutAlerts = Student::factory()->count(5)->create();

    livewire(ListStudents::class)
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($studentsWithoutAlerts->merge([$studentWithStatusActive, $studentWithStatusInprogress]))
        ->filterTable('alerts', [$activeStatusAlert, $inprogressStatusAlert])
        ->assertCanSeeTableRecords([$studentWithStatusActive, $studentWithStatusInprogress])
        ->assertCanNotSeeTableRecords($studentsWithoutAlerts)
        ->resetTableFilters()
        ->filterTable('alerts', [$activeStatusAlert])
        ->assertCanSeeTableRecords([$studentWithStatusActive])
        ->assertCanNotSeeTableRecords($studentsWithoutAlerts->merge([$studentWithStatusInprogress]))
        ->removeTableFilter('alerts')
        ->assertCanSeeTableRecords($studentsWithoutAlerts->merge([$studentWithStatusActive, $studentWithStatusInprogress]));
});

it('renders the bulk create interaction action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionHidden('createInteraction');

    $user->givePermissionTo('interaction.create');

    livewire(ListStudents::class)
        ->assertOk()
        ->assertTableBulkActionVisible('createInteraction');
});
