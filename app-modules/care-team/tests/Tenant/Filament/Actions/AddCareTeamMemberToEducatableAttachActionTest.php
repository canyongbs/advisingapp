<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CareTeam\Filament\Actions\AddCareTeamMemberToEducatableAttachAction;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ViewProspect;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can attach users without a care team role to a student', function () {
    asSuperAdmin();

    $student = Student::factory()->create();
    $user1 = User::factory()->licensed(LicenseType::cases())->create();
    $user2 = User::factory()->licensed(LicenseType::cases())->create();

    livewire(ViewStudent::class, ['record' => $student->getKey()])
        ->mountAction(AddCareTeamMemberToEducatableAttachAction::class)
        ->fillForm([
            'careTeams' => [
                [
                    'user_id' => $user1->getKey(),
                    'care_team_role_id' => null,
                ],
                [
                    'user_id' => $user2->getKey(),
                    'care_team_role_id' => null,
                ],
            ]
        ])
        ->callMountedAction();

    $student->refresh();

    expect($student->careTeam()->count())->toBe(2);
});