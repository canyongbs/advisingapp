<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ManageCaseTypeManagers;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can attach team member to case type', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $caseType = CaseType::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('case-type-managers', [
                'record' => $caseType->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('case_type.view-any');
    $user->givePermissionTo('team.view-any');

    livewire(ManageCaseTypeManagers::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $team->getKey()]
        )->assertSuccessful();

    expect($caseType->refresh())
        ->managers
        ->pluck('id')
        ->toContain($team->getKey());
});
