<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Interaction\Filament\Resources\InteractionDrivers\Pages\ListInteractionDrivers;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\Pages\ListInteractionInitiatives;
use AdvisingApp\Interaction\Filament\Resources\InteractionRelations\Pages\ListInteractionRelations;
use AdvisingApp\Interaction\Filament\Resources\Interactions\Pages\CreateInteraction;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatuses\Pages\ListInteractionStatuses;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $settings = app(InteractionManagementSettings::class);
    $settings->is_initiative_enabled = true;
    $settings->is_initiative_required = true;
    $settings->is_driver_enabled = true;
    $settings->is_driver_required = true;
    $settings->is_outcome_enabled = true;
    $settings->is_outcome_required = true;
    $settings->is_relation_enabled = true;
    $settings->is_relation_required = true;
    $settings->is_status_enabled = true;
    $settings->is_status_required = true;
    $settings->is_type_enabled = true;
    $settings->is_type_required = true;
    $settings->save();
});

it('disables initiative setting from the initiatives list page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo([
        'settings.view-any',
    ]);

    actingAs($user);

    livewire(ListInteractionInitiatives::class)
        ->assertFormSet([
            'is_initiative_enabled' => true,
            'is_initiative_required' => true,
        ])
        ->fillForm([
            'is_initiative_enabled' => false,
        ])
        ->assertHasNoFormErrors();

    expect(app(InteractionManagementSettings::class)->is_initiative_enabled)->toBeFalse();
});

it('makes driver optional from the drivers list page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo([
        'settings.view-any',
    ]);

    actingAs($user);

    livewire(ListInteractionDrivers::class)
        ->fillForm([
            'is_driver_required' => false,
        ])
        ->assertHasNoFormErrors();

    expect(app(InteractionManagementSettings::class)->is_driver_required)->toBeFalse();
});

it('persists settings across different list pages', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo([
        'settings.view-any',
    ]);

    actingAs($user);

    livewire(ListInteractionRelations::class)
        ->fillForm([
            'is_relation_enabled' => false,
            'is_relation_required' => false,
        ]);

    livewire(ListInteractionStatuses::class)
        ->assertFormSet([
            'is_relation_enabled' => false,
            'is_relation_required' => false,
        ]);
});

it('initializes all interaction settings with correct defaults', function () {
    $settings = new InteractionManagementSettings();

    expect($settings->is_initiative_enabled)->toBeTrue()
        ->and($settings->is_initiative_required)->toBeTrue()
        ->and($settings->is_driver_enabled)->toBeTrue()
        ->and($settings->is_driver_required)->toBeTrue()
        ->and($settings->is_outcome_enabled)->toBeTrue()
        ->and($settings->is_outcome_required)->toBeTrue()
        ->and($settings->is_relation_enabled)->toBeTrue()
        ->and($settings->is_relation_required)->toBeTrue()
        ->and($settings->is_status_enabled)->toBeTrue()
        ->and($settings->is_status_required)->toBeTrue()
        ->and($settings->is_type_enabled)->toBeTrue()
        ->and($settings->is_type_required)->toBeTrue();
});

it('persists updated interaction settings after save', function () {
    $settings = app(InteractionManagementSettings::class);
    $settings->is_initiative_enabled = false;
    $settings->is_driver_required = false;
    $settings->save();

    $reloadedSettings = app(InteractionManagementSettings::class);

    expect($reloadedSettings->is_initiative_enabled)->toBeFalse()
        ->and($reloadedSettings->is_driver_required)->toBeFalse()
        ->and($reloadedSettings->is_outcome_enabled)->toBeTrue();
});

$fieldTypes = [
    'initiative' => 'interaction_initiative_id',
    'driver' => 'interaction_driver_id',
    'outcome' => 'interaction_outcome_id',
    'relation' => 'interaction_relation_id',
    'status' => 'interaction_status_id',
    'type' => 'interaction_type_id',
];

foreach ($fieldTypes as $fieldType => $fieldName) {
    it("hides {$fieldType} field in create form when {$fieldType} is disabled", function () use ($fieldType, $fieldName) {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo([
            'interaction.view-any',
            'interaction.create',
        ]);

        $settings = app(InteractionManagementSettings::class);
        $settings->{"is_{$fieldType}_enabled"} = false;
        $settings->save();

        actingAs($user);

        livewire(CreateInteraction::class)
            ->assertFormFieldIsHidden($fieldName);
    });

    it("allows saving interaction without {$fieldType} when {$fieldType} is not mandatory", function () use ($fieldType, $fieldName) {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo([
            'interaction.view-any',
            'interaction.create',
        ]);

        $student = Student::factory()->create();

        $settings = app(InteractionManagementSettings::class);
        $settings->{"is_{$fieldType}_required"} = false;
        $settings->save();

        actingAs($user);

        livewire(CreateInteraction::class)
            ->fillForm([
                'interactable_type' => Student::class,
                'interactable_id' => $student->getKey(),
                'subject' => 'Test interaction',
                'description' => 'Test description',
                $fieldName => null,
            ])
            ->call('create')
            ->assertHasNoFormErrors([$fieldName]);
    });
}
