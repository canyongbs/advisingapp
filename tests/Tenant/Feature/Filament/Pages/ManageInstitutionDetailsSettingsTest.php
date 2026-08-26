<?php

use App\Filament\Pages\ManageInstitutionDetailsSettings;
use App\Models\User;
use App\Settings\InstitutionDetailsSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('requires proper permissions to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageInstitutionDetailsSettings::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageInstitutionDetailsSettings::getUrl())
        ->assertOk();
});

it('disables the form without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageInstitutionDetailsSettings::class)
        ->assertFormFieldDisabled('ipeds_id')
        ->assertFormFieldDisabled('name')
        ->assertFormFieldDisabled('dark_logo')
        ->assertFormFieldDisabled('light_logo');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageInstitutionDetailsSettings::class)
        ->assertFormFieldEnabled('ipeds_id')
        ->assertFormFieldEnabled('name')
        ->assertFormFieldEnabled('dark_logo')
        ->assertFormFieldEnabled('light_logo');
});

it('hides the `save` action without the `settings.*.update` permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageInstitutionDetailsSettings::class)
        ->assertActionDoesNotExist(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));

    $user->givePermissionTo('settings.*.update');

    livewire(ManageInstitutionDetailsSettings::class)
        ->assertActionVisible(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));
});

it('requires proper permissions to update settings', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $settings = app(InstitutionDetailsSettings::class);
    $settings->name = 'Existing Institution Name';
    $settings->save();

    livewire(ManageInstitutionDetailsSettings::class)
        ->fillForm([
            'name' => 'New Institution Name',
        ])
        ->call('save');

    expect($settings->name)->toBe('Existing Institution Name');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageInstitutionDetailsSettings::class)
        ->fillForm([
            'name' => 'New Institution Name',
        ])
        ->call('save');

    expect($settings->name)->toBe('New Institution Name');
});
