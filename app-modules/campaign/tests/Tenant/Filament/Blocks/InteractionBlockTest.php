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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\ViewCampaign;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\RelationManagers\CampaignActionsRelationManager;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Campaign\Models\CampaignAction;
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
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();
});

it('shows interaction options only for campaign population model type when editing journey step', function () {
    $campaign = Campaign::factory()
        ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
        ->for(Group::factory()->state(['model' => GroupModel::Student]), 'group')
        ->create();

    $studentInitiative = InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Student]);
    $prospectInitiative = InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Prospect]);
    $studentDriver = InteractionDriver::factory()->create(['interactable_type' => InteractableType::Student]);
    $prospectDriver = InteractionDriver::factory()->create(['interactable_type' => InteractableType::Prospect]);

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Interaction,
            'execute_at' => now()->addDay(),
            'data' => [
                'interaction_initiative_id' => $studentInitiative->getKey(),
                'interaction_driver_id' => $studentDriver->getKey(),
                'start_datetime' => now()->toDateTimeString(),
                'end_datetime' => now()->addHour()->toDateTimeString(),
                'subject' => 'Test subject',
                'description' => 'Test description',
            ],
        ]);

    livewire(CampaignActionsRelationManager::class, [
        'ownerRecord' => $campaign,
        'pageClass' => ViewCampaign::class,
    ])
        ->mountAction(TestAction::make('edit')->table($action))
        ->assertFormFieldExists('data.interaction_initiative_id', checkFieldUsing: function (Select $field) use ($studentInitiative, $prospectInitiative) {
            $optionKeys = array_keys($field->getOptions());

            expect($optionKeys)->toContain($studentInitiative->getKey())
                ->and($optionKeys)->not->toContain($prospectInitiative->getKey());

            return true;
        })
        ->assertFormFieldExists('data.interaction_driver_id', checkFieldUsing: function (Select $field) use ($studentDriver, $prospectDriver) {
            $optionKeys = array_keys($field->getOptions());

            expect($optionKeys)->toContain($studentDriver->getKey())
                ->and($optionKeys)->not->toContain($prospectDriver->getKey());

            return true;
        });
});

it('shows interaction options only for campaign population model type when editing journey step of a prospect campaign', function () {
    $prospectGroup = Group::factory()->state(['model' => GroupModel::Prospect])->create();

    $studentInitiative = InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Student]);
    $prospectInitiative = InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Prospect]);
    $studentDriver = InteractionDriver::factory()->create(['interactable_type' => InteractableType::Student]);
    $prospectDriver = InteractionDriver::factory()->create(['interactable_type' => InteractableType::Prospect]);

    /** @var CampaignAction $action */
    $action = CampaignAction::factory()
        ->for(
            Campaign::factory()
                ->for(User::factory()->licensed(LicenseType::cases()), 'createdBy')
                ->for($prospectGroup, 'group')
                ->create(),
            'campaign'
        )
        ->create([
            'type' => CampaignActionType::Interaction,
            'execute_at' => now()->addDay(),
            'data' => [
                'interaction_initiative_id' => $prospectInitiative->getKey(),
                'interaction_driver_id' => $prospectDriver->getKey(),
                'start_datetime' => now()->toDateTimeString(),
                'end_datetime' => now()->addHour()->toDateTimeString(),
                'subject' => 'Test subject',
                'description' => 'Test description',
            ],
        ]);

    livewire(CampaignActionsRelationManager::class, [
        'ownerRecord' => $action->campaign,
        'pageClass' => ViewCampaign::class,
    ])
        ->mountAction(TestAction::make('edit')->table($action))
        ->assertFormFieldExists('data.interaction_initiative_id', checkFieldUsing: function (Select $field) use ($studentInitiative, $prospectInitiative) {
            $optionKeys = array_keys($field->getOptions());

            expect($optionKeys)->toContain($prospectInitiative->getKey())
                ->and($optionKeys)->not->toContain($studentInitiative->getKey());

            return true;
        })
        ->assertFormFieldExists('data.interaction_driver_id', checkFieldUsing: function (Select $field) use ($studentDriver, $prospectDriver) {
            $optionKeys = array_keys($field->getOptions());

            expect($optionKeys)->toContain($prospectDriver->getKey())
                ->and($optionKeys)->not->toContain($studentDriver->getKey());

            return true;
        });
});

it('shows interaction options only for the selected population model type in the create wizard', function (GroupModel $groupModel, InteractableType $expected, InteractableType $excluded) {
    $group = Group::factory()->create(['model' => $groupModel, 'user_id' => auth()->id()]);

    $selects = [
        'interaction_initiative_id' => InteractionInitiative::class,
        'interaction_driver_id' => InteractionDriver::class,
        'interaction_outcome_id' => InteractionOutcome::class,
        'interaction_relation_id' => InteractionRelation::class,
        'interaction_status_id' => InteractionStatus::class,
        'interaction_type_id' => InteractionType::class,
    ];

    $expectedRecords = [];
    $excludedRecords = [];

    foreach ($selects as $field => $model) {
        $expectedRecords[$field] = $model::factory()->create(['interactable_type' => $expected]);
        $excludedRecords[$field] = $model::factory()->create(['interactable_type' => $excluded]);
    }

    $component = livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
            'segment_id' => $group->getKey(),
            'actions' => [
                'step-one' => [
                    'type' => 'interaction',
                    'data' => [
                        ...array_map(fn ($record) => $record->getKey(), $expectedRecords),
                        'start_datetime' => now()->toDateTimeString(),
                        'end_datetime' => now()->addHour()->toDateTimeString(),
                        'subject' => 'Test subject',
                        'description' => 'Test description',
                        'execute_at' => now()->addDay()->toDateTimeString(),
                    ],
                ],
            ],
        ]);

    foreach (array_keys($selects) as $field) {
        $component->assertFormFieldExists(
            "actions.step-one.data.{$field}",
            checkFieldUsing: function (Select $select) use ($expectedRecords, $excludedRecords, $field) {
                $optionKeys = array_keys($select->getOptions());

                expect($optionKeys)->toContain($expectedRecords[$field]->getKey())
                    ->and($optionKeys)->not->toContain($excludedRecords[$field]->getKey());

                return true;
            }
        );
    }
})->with([
    'student population group' => [GroupModel::Student, InteractableType::Student, InteractableType::Prospect],
    'prospect population group' => [GroupModel::Prospect, InteractableType::Prospect, InteractableType::Student],
]);

it('offers no interaction options in the create wizard until a population group is selected', function () {
    InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Student]);
    InteractionInitiative::factory()->create(['interactable_type' => InteractableType::Prospect]);

    livewire(CreateCampaign::class)
        ->fillForm([
            'name' => 'Test Campaign',
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
        ->assertFormFieldExists('actions.step-one.data.interaction_initiative_id', checkFieldUsing: function (Select $select) {
            expect($select->getOptions())->toBeEmpty();

            return true;
        });
});
