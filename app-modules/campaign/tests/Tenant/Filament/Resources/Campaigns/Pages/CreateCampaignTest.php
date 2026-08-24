<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Tests\Tenant\Filament\Resources\Campaigns\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Interaction\Enums\InteractableType;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use App\Models\User;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Spatie\Permission\PermissionRegistrar;

use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();
});

it('clears journey steps when the population group is changed', function (GroupModel $newPopulationModel) {
    $studentGroup = Group::factory()->create(['model' => GroupModel::Student, 'user_id' => auth()->id()]);
    $newGroup = Group::factory()->create(['model' => $newPopulationModel, 'user_id' => auth()->id()]);

    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
            'segment_id' => $studentGroup->getKey(),
            'actions' => [
                'step-one' => [
                    'type' => 'interaction',
                    'data' => [
                        'interaction_initiative_id' => InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'interaction_driver_id' => InteractionDriver::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'interaction_outcome_id' => InteractionOutcome::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'interaction_relation_id' => InteractionRelation::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'interaction_status_id' => InteractionStatus::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'interaction_type_id' => InteractionType::factory()->create(['interactable_type' => InteractableType::Student])->getKey(),
                        'start_datetime' => now()->toDateTimeString(),
                        'end_datetime' => now()->addHour()->toDateTimeString(),
                        'subject' => 'Test subject',
                        'description' => 'Test description',
                        'execute_at' => now()->addDay()->toDateTimeString(),
                    ],
                ],
            ],
        ])
        ->assertSchemaStateSet(['actions.step-one.data.subject' => 'Test subject'])
        ->fillForm(['segment_id' => $newGroup->getKey()])
        ->assertSchemaStateSet([
            'segment_id' => $newGroup->getKey(),
            'actions' => [],
        ]);
})->with([
    'to a group of a different population model type' => GroupModel::Prospect,
    'to another group of the same population model type' => GroupModel::Student,
]);

it('requires population group on campaign creation', function () {
    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'segment_id' => 'required',
        ]);
});

it('only offers the user\'s own and department\'s population groups when they lack the group.*.view permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('campaign.view-any');
    $user->givePermissionTo('campaign.create');

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    actingAs($user);

    $otherUsersGroup = Group::factory()->create();

    livewire(CreateCampaign::class)
        ->assertFormFieldExists(
            'segment_id',
            function (Select $field) use ($otherUsersGroup) {
                expect($field->getOptions())->not->toHaveKey($otherUsersGroup->getKey());

                return true;
            },
        );
});

it('offers every population group when the user has the group.*.view permission', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('campaign.view-any');
    $user->givePermissionTo('campaign.create');
    $user->givePermissionTo('group.*.view');

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    actingAs($user);

    $otherUsersGroup = Group::factory()->create();

    livewire(CreateCampaign::class)
        ->assertFormFieldExists(
            'segment_id',
            function (Select $field) use ($otherUsersGroup) {
                expect($field->getOptions())->toHaveKey($otherUsersGroup->getKey());

                return true;
            },
        );
});
