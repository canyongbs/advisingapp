<?php

use App\Filament\Pages\ManageDisplaySettings;
use App\Models\User;
use App\Settings\DisplaySettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('requires proper permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageDisplaySettings::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageDisplaySettings::getUrl())
        ->assertOk();
});

it('disables the form without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageDisplaySettings::class)
        ->assertFormFieldDisabled('timezone');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageDisplaySettings::class)
        ->assertFormFieldEnabled('timezone');
});

it('hides the `save` action without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageDisplaySettings::class)
        ->assertActionDoesNotExist(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));

    $user->givePermissionTo('settings.*.update');

    livewire(ManageDisplaySettings::class)
        ->assertActionVisible(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));
});

it('requires proper permissions to update settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $settings = app(DisplaySettings::class);
    $settings->timezone = 'America/Chicago';
    $settings->save();

    livewire(ManageDisplaySettings::class)
        ->fillForm([
            'timezone' => 'America/New_York',
        ])
        ->call('save');

    expect($settings->timezone)->toBe('America/Chicago');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageDisplaySettings::class)
        ->fillForm([
            'timezone' => 'America/New_York',
        ])
        ->call('save');

    expect($settings->timezone)->toBe('America/New_York');
});
