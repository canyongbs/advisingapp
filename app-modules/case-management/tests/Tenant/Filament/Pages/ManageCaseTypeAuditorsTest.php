<?php

use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ManageCaseTypeAuditors;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can attach audit member to case type', function () {
    $user = User::factory()->licensed([Student::getLicenseType(), Prospect::getLicenseType()])->create();

    $caseType = CaseType::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            CaseTypeResource::getUrl('case-type-auditors', [
                'record' => $caseType->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('team.view-any');

    livewire(ManageCaseTypeAuditors::class, [
        'record' => $caseType->getRouteKey(),
    ])
        ->callTableAction(
            AttachAction::class,
            data: ['recordId' => $team->getKey()]
        )->assertSuccessful();

    expect($caseType->refresh())
        ->auditors
        ->pluck('id')
        ->toContain($team->getKey());
});
