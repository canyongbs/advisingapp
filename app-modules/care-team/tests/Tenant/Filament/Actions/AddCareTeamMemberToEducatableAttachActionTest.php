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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Filament\Resources\Prospects\Pages\ManageProspectCareTeam;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\ManageStudentCareTeam;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can attach users without a care team role to a student', function () {
    Repeater::fake();
    asSuperAdmin();

    $student = Student::factory()->create();
    $user1 = User::factory()->licensed(LicenseType::cases())->create();
    $user2 = User::factory()->licensed(LicenseType::cases())->create();

    livewire(ManageStudentCareTeam::class, ['record' => $student->getKey()])
        ->mountAction(TestAction::make('attach')->table())
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
            ],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $student->refresh();

    expect($student->careTeam()->count())->toBe(2);
    expect($student->careTeam->pluck('id'))->toContain($user1->getKey());
    expect($student->careTeam->pluck('id'))->toContain($user2->getKey());
});

it('can attach users without a care team role to a prospect', function () {
    Repeater::fake();
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    $user1 = User::factory()->licensed(LicenseType::cases())->create();
    $user2 = User::factory()->licensed(LicenseType::cases())->create();

    livewire(ManageProspectCareTeam::class, ['record' => $prospect->getKey()])
        ->mountAction(TestAction::make('attach')->table())
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
            ],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $prospect->refresh();

    expect($prospect->careTeam()->count())->toBe(2);
    expect($prospect->careTeam->pluck('id'))->toContain($user1->getKey());
    expect($prospect->careTeam->pluck('id'))->toContain($user2->getKey());
});

it('can attach users with care team roles to a student', function () {
    Repeater::fake();
    asSuperAdmin();

    $student = Student::factory()->create();
    $user1 = User::factory()->licensed(LicenseType::cases())->create();
    $user2 = User::factory()->licensed(LicenseType::cases())->create();
    $careTeamRole1 = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Student]);
    $careTeamRole2 = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Student]);

    livewire(ManageStudentCareTeam::class, ['record' => $student->getKey()])
        ->mountAction(TestAction::make('attach')->table())
        ->fillForm([
            'careTeams' => [
                [
                    'user_id' => $user1->getKey(),
                    'care_team_role_id' => $careTeamRole1->getKey(),
                ],
                [
                    'user_id' => $user2->getKey(),
                    'care_team_role_id' => $careTeamRole2->getKey(),
                ],
            ],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $student->refresh();

    expect($student->careTeam()->count())->toBe(2);
    expect($student->careTeam->pluck('id'))->toContain($user1->getKey());
    expect($student->careTeam->pluck('id'))->toContain($user2->getKey());
    expect(CareTeam::where('user_id', $user1->getKey())->where('educatable_id', $student->getKey())->first()->care_team_role_id)->toBe($careTeamRole1->getKey());
    expect(CareTeam::where('user_id', $user2->getKey())->where('educatable_id', $student->getKey())->first()->care_team_role_id)->toBe($careTeamRole2->getKey());
});

it('can attach users with care team roles to a prospect', function () {
    Repeater::fake();
    asSuperAdmin();

    $prospect = Prospect::factory()->create();
    $user1 = User::factory()->licensed(LicenseType::cases())->create();
    $user2 = User::factory()->licensed(LicenseType::cases())->create();
    $careTeamRole1 = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);
    $careTeamRole2 = CareTeamRole::factory()->create(['type' => CareTeamRoleType::Prospect]);

    livewire(ManageProspectCareTeam::class, ['record' => $prospect->getKey()])
        ->mountAction(TestAction::make('attach')->table())
        ->fillForm([
            'careTeams' => [
                [
                    'user_id' => $user1->getKey(),
                    'care_team_role_id' => $careTeamRole1->getKey(),
                ],
                [
                    'user_id' => $user2->getKey(),
                    'care_team_role_id' => $careTeamRole2->getKey(),
                ],
            ],
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $prospect->refresh();

    expect($prospect->careTeam()->count())->toBe(2);
    expect($prospect->careTeam->pluck('id'))->toContain($user1->getKey());
    expect($prospect->careTeam->pluck('id'))->toContain($user2->getKey());
    expect(CareTeam::where('user_id', $user1->getKey())->where('educatable_id', $prospect->getKey())->first()->care_team_role_id)->toBe($careTeamRole1->getKey());
    expect(CareTeam::where('user_id', $user2->getKey())->where('educatable_id', $prospect->getKey())->first()->care_team_role_id)->toBe($careTeamRole2->getKey());
});
