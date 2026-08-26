<?php

use AdvisingApp\Theme\Filament\Pages\ManageCollegeBrandingSettings;
use App\Models\User;
use App\Settings\CollegeBrandingSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('requires proper permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageCollegeBrandingSettings::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageCollegeBrandingSettings::getUrl())
        ->assertOk();
});

it('disables the form without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageCollegeBrandingSettings::class)
        ->assertFormFieldDisabled('is_enabled')
        ->assertFormFieldDisabled('dismissible')
        ->assertFormFieldDisabled('college_text')
        ->assertFormFieldDisabled('color');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageCollegeBrandingSettings::class)
        ->assertFormFieldEnabled('is_enabled')
        ->assertFormFieldEnabled('dismissible')
        ->assertFormFieldEnabled('college_text')
        ->assertFormFieldEnabled('color');
});

it('hides the `save` action without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageCollegeBrandingSettings::class)
        ->assertActionDoesNotExist(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));

    $user->givePermissionTo('settings.*.update');

    livewire(ManageCollegeBrandingSettings::class)
        ->assertActionVisible(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));
});

it('requires proper permissions to update settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $settings = app(CollegeBrandingSettings::class);
    $settings->is_enabled = true;
    $settings->dismissible = true;
    $settings->college_text = 'Existing College Text';
    $settings->color = Color::Blue;
    $settings->save();

    livewire(ManageCollegeBrandingSettings::class)
        ->fillForm([
            'is_enabled' => true,
            'dismissible' => false,
            'college_text' => 'New College Text',
            'color' => Color::Red->value,
        ])
        ->call('save');

    expect($settings->college_text)->toBe('Existing College Text')
        ->and($settings->color)->toBe(Color::Blue);

    $user->givePermissionTo('settings.*.update');

    livewire(ManageCollegeBrandingSettings::class)
        ->fillForm([
            'is_enabled' => true,
            'dismissible' => false,
            'college_text' => 'New College Text',
            'color' => Color::Red->value,
        ])
        ->call('save');

    expect($settings->college_text)->toBe('New College Text')
        ->and($settings->color)->toBe(Color::Red);
});