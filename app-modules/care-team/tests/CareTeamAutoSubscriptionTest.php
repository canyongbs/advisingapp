<?php

namespace AdvisingApp\CareTeam\Tests;

use App\Models\User;

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;

use AdvisingApp\Prospect\Models\Prospect;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\assertDatabaseHas;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectCareTeam;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentCareTeam;

it('can auto subscribe user to prospect upon adding in care team', function () {
    $superAdmin = User::factory()->licensed(Prospect::getLicenseType())->create();

    $careTeamMember = User::factory()->licensed(Prospect::getLicenseType())->create();

    asSuperAdmin($superAdmin);

    $prospect = Prospect::factory()->create();

    livewire(ManageProspectCareTeam::class, [
        'record' => $prospect->getKey(),
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => [$careTeamMember->getKey()]]
        )->assertSuccessful();

    $prospect->refresh();

    expect($prospect->careTeam->pluck('id'))->toContain($careTeamMember->getKey());

    assertDatabaseHas('care_teams', [
        'educatable_id' => $prospect->getKey(),
        'user_id' => $careTeamMember->getKey(),
    ]);
});

it('can auto subscribe user to student upon adding in care team', function () {
    $superAdmin = User::factory()->licensed(Student::getLicenseType())->create();

    $careTeamMember = User::factory()->licensed(Student::getLicenseType())->create();

    asSuperAdmin($superAdmin);

    $student = Student::factory()->create();

    livewire(ManageStudentCareTeam::class, [
        'record' => $student->getKey(),
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => [$careTeamMember->getKey()]]
        )->assertSuccessful();

    $student->refresh();

    expect($student->careTeam->pluck('id'))->toContain($careTeamMember->getKey());

    assertDatabaseHas('care_teams', [
        'educatable_id' => $student->getKey(),
        'user_id' => $careTeamMember->getKey(),
    ]);
});
