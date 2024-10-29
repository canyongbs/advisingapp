<?php

use App\Models\User;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Livewire\ManageStudentPremiumFeatures;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentFormSubmissionsRelationManager;

it('requires proper access', function () {
    $user = User::factory()->licensed(Student::getLicenseType())->create();
    $student = Student::factory()->create();

    actingAs($user);

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('student.view-any');
    $user->givePermissionTo('student.*.view');

    $user->refresh();

    actingAs($user);

    livewire(ViewStudent::class, [
        'record' => $student->getKey(),
    ])
        ->assertOk();
});

it('renders the StudentFormSubmissionsRelationManager based on Feature access', function () {
    $student = Student::factory()->create();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->addons->onlineForms = false;

    $licenseSettings->save();

    asSuperAdmin();

    $relationManager = StudentFormSubmissionsRelationManager::class;

    livewire(ManageStudentPremiumFeatures::class, [
        'record' => $student->getKey(),
        'activeRelationManager' => array_search(
            $relationManager,
            (new ManageStudentPremiumFeatures())
                ->tap(fn (ManageStudentPremiumFeatures $manager) => $manager->mount($student->getKey()))
                ->getRelationManagers()
        ),
    ])
        ->assertOk()
        ->assertDontSeeLivewire($relationManager);
});
