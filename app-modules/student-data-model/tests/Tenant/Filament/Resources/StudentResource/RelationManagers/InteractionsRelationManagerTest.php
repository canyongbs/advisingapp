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

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders the InteractionsRelationManager based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    actingAs($user);

    $relationManager = InteractionsRelationManager::class;

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);

    $user->givePermissionTo('interaction.view-any');

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk()
        ->assertSeeLivewire($relationManager);
});

it('renders only the interactions associated with student', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Interaction::factory()->count(5))
        ->create();

    $notAssociatedStudent = Student::factory()
        ->has(Interaction::factory()->count(5))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertCanSeeTableRecords($student->interactions)
        ->assertCanNotSeeTableRecords($notAssociatedStudent->interactions);
});

it('renders the initiative select filter', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $initiatives = InteractionInitiative::factory()->count(2)->create();
    $selectableInteractions = Interaction::factory([
        'interaction_initiative_id' => $initiatives->first()->getKey(),
    ])->create();

    $nonSelectableInteractions = Interaction::factory([
        'interaction_initiative_id' => $initiatives->last()->getKey(),
    ])->create();

    $student = Student::factory()->create();

    $student->interactions()->saveMany([$selectableInteractions, $nonSelectableInteractions]);

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('interaction_initiative_id', $initiatives->first()->getKey())
        ->assertCanSeeTableRecords([$selectableInteractions])
        ->assertCanNotSeeTableRecords([$nonSelectableInteractions])
        ->removeTableFilter('interaction_initiative_id')
        ->assertCanSeeTableRecords([$selectableInteractions, $nonSelectableInteractions]);
});

it('renders the driver select filter', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $drivers = InteractionDriver::factory()->count(2)->create();
    $selectableInteractions = Interaction::factory([
        'interaction_driver_id' => $drivers->first()->getKey(),
    ])->create();

    $nonSelectableInteractions = Interaction::factory([
        'interaction_driver_id' => $drivers->last()->getKey(),
    ])->create();

    $student = Student::factory()->create();

    $student->interactions()->saveMany([$selectableInteractions, $nonSelectableInteractions]);

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('interaction_driver_id', $drivers->first()->getKey())
        ->assertCanSeeTableRecords([$selectableInteractions])
        ->assertCanNotSeeTableRecords([$nonSelectableInteractions])
        ->removeTableFilter('interaction_driver_id')
        ->assertCanSeeTableRecords([$selectableInteractions, $nonSelectableInteractions]);
});

it('renders the type select filter', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $types = InteractionType::factory()->count(2)->create();
    $selectableInteractions = Interaction::factory([
        'interaction_type_id' => $types->first()->getKey(),
    ])->create();

    $nonSelectableInteractions = Interaction::factory([
        'interaction_type_id' => $types->last()->getKey(),
    ])->create();

    $student = Student::factory()->create();

    $student->interactions()->saveMany([$selectableInteractions, $nonSelectableInteractions]);

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('interaction_type_id', $types->first()->getKey())
        ->assertCanSeeTableRecords([$selectableInteractions])
        ->assertCanNotSeeTableRecords([$nonSelectableInteractions])
        ->removeTableFilter('interaction_type_id')
        ->assertCanSeeTableRecords([$selectableInteractions, $nonSelectableInteractions]);
});

it('renders the status select filter', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $statuses = InteractionStatus::factory()->count(5)->create();
    $selectableInteractions = Interaction::factory([
        'interaction_status_id' => $statuses->first()->getKey(),
    ])->create();

    $nonSelectableInteractions = Interaction::factory([
        'interaction_status_id' => $statuses->last()->getKey(),
    ])->create();

    $student = Student::factory()->create();

    $student->interactions()->saveMany([$selectableInteractions, $nonSelectableInteractions]);

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('interaction_status_id', $statuses->first()->getKey())
        ->assertCanSeeTableRecords([$selectableInteractions])
        ->assertCanNotSeeTableRecords([$nonSelectableInteractions])
        ->removeTableFilter('interaction_status_id')
        ->assertCanSeeTableRecords([$nonSelectableInteractions, $selectableInteractions]);
});

it('renders the created by select filter', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $users = User::factory()->count(2)->create();
    $selectableInteractions = Interaction::factory([
        'user_id' => $users->first()->getKey(),
    ])->create();

    $nonSelectableInteractions = Interaction::factory([
        'user_id' => $users->last()->getKey(),
    ])->create();

    $student = Student::factory()->create();

    $student->interactions()->saveMany([$selectableInteractions, $nonSelectableInteractions]);

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->filterTable('user_id', $users->first()->getKey())
        ->assertCanSeeTableRecords([$selectableInteractions])
        ->assertCanNotSeeTableRecords([$nonSelectableInteractions])
        ->removeTableFilter('user_id')
        ->assertCanSeeTableRecords([$selectableInteractions, $nonSelectableInteractions]);
});
