<?php

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Interaction\Filament\Resources\InteractionDriverResource\Pages\ListInteractionDrivers;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiativeResource\Pages\ListInteractionInitiatives;
use AdvisingApp\Interaction\Filament\Resources\InteractionRelationResource\Pages\ListInteractionRelations;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatusResource\Pages\ListInteractionStatuses;
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
