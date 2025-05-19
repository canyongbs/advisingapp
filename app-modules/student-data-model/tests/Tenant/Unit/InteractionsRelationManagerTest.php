<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders the Create Interactions Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Interaction::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.create');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(CreateAction::class);
});

it('renders the Edit Interaction Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Interaction::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.*.view');
    $user->givePermissionTo('interaction.update');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(EditAction::class, $student->interactions->first());
});

it('renders the Delete Interaction Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Interaction::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.*.view');
    $user->givePermissionTo('interaction.delete');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(DeleteAction::class, $student->interactions->first());
});

it('renders the Delete Bulk Intractions Table Action based on proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();

    $student = Student::factory()
        ->has(Interaction::factory()->count(1))
        ->create();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');
    $user->givePermissionTo('interaction.view-any');
    $user->givePermissionTo('interaction.*.view');
    $user->givePermissionTo('interaction.delete');

    actingAs($user);

    livewire(InteractionsRelationManager::class, [
        'ownerRecord' => $student,
        'pageClass' => ViewStudent::class,
    ])
        ->assertOk()
        ->assertTableActionVisible(DeleteAction::class);
});
