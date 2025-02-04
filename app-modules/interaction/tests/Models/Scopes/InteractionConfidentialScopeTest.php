<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\Scopes\InteractionConfidentialScope;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('Interaction model has applied global scope', function(){

    $interaction = Interaction::factory()->create();

    $this->assertTrue($interaction->hasGlobalScope(InteractionConfidentialScope::class));
});

test('InteractionsRelationManager with display data for created user', function(){
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);
    $confidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
        'user_id' => $user,
    ]);

    $otherConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $nonConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $student = Student::factory()
        ->create();

    $student->interactions()->saveMany($confidentialInteraction->merge($otherConfidentialInteraction)->merge($nonConfidentialInteraction));
    $student->refresh();

    livewire(
        InteractionsRelationManager::class,
        [
            'ownerRecord' => $student,
            'pageClass' => ViewStudent::class,
        ]
    )
        ->set('tableRecordsPerPage', 20)
        ->assertCanSeeTableRecords($confidentialInteraction->merge($nonConfidentialInteraction))
        ->assertCanNotSeeTableRecords($otherConfidentialInteraction);
});

test('InteractionsRelationManager with display data for team user', function(){

    $teamUser = User::factory()->licensed(LicenseType::cases())->create();
    $teamUser->givePermissionTo('interaction.view-any');

    $team = Team::factory()->hasAttached($teamUser, [], 'users')->create();

    actingAs($teamUser);
    $confidentialInteraction = Interaction::factory()->hasAttached($team, [], 'confidentialAccessTeams')->count(10)->create([
        'is_confidential' => true,
    ]);

    $otherConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);
$nonConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $student = Student::factory()
        ->create();

    $student->interactions()->saveMany($confidentialInteraction->merge($otherConfidentialInteraction)->merge($nonConfidentialInteraction));
    $student->refresh();

    livewire(
        InteractionsRelationManager::class,
        [
            'ownerRecord' => $student,
            'pageClass' => ViewStudent::class,
        ]
    )
        ->set('tableRecordsPerPage', 20)
        ->assertCanSeeTableRecords($confidentialInteraction->merge($nonConfidentialInteraction))
        ->assertCanNotSeeTableRecords($otherConfidentialInteraction);
});

test('InteractionsRelationManager with display data for assigned user', function(){

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('interaction.view-any');

    actingAs($user);
    $confidentialInteraction = Interaction::factory()->hasAttached($user, [], 'confidentialAccessUsers')->count(10)->create([
        'is_confidential' => true,
    ]);

    $otherConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

$nonConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $student = Student::factory()
        ->create();

    $student->interactions()->saveMany($confidentialInteraction->merge($otherConfidentialInteraction)->merge($nonConfidentialInteraction));
    $student->refresh();

    livewire(
        InteractionsRelationManager::class,
        [
            'ownerRecord' => $student,
            'pageClass' => ViewStudent::class,
        ]
    )
        ->set('tableRecordsPerPage', 20)
        ->assertCanSeeTableRecords($confidentialInteraction->merge($nonConfidentialInteraction))
        ->assertCanNotSeeTableRecords($otherConfidentialInteraction);
});

test('InteractionsRelationManager with display all data for superadmin user', function(){

    asSuperAdmin();
    $confidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $nonConfidentialInteraction = Interaction::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $student = Student::factory()
        ->create();

    $student->interactions()->saveMany($confidentialInteraction->merge($nonConfidentialInteraction));
    $student->refresh();

    livewire(
        InteractionsRelationManager::class,
        [
            'ownerRecord' => $student,
            'pageClass' => ViewStudent::class,
        ]
    )
        ->set('tableRecordsPerPage', 20)
        ->assertCanSeeTableRecords($confidentialInteraction->merge($nonConfidentialInteraction));
    });
