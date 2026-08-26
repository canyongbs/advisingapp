<?php

use AdvisingApp\Prospect\Filament\Pages\ManageProspectConversionSettings;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\User;
use App\Settings\ProspectConversionSettings;
use Cknow\Money\Money;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('requires a prospect license and the proper permission to access', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ManageProspectConversionSettings::getUrl())
        ->assertForbidden();

    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    actingAs($user);

    get(ManageProspectConversionSettings::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ManageProspectConversionSettings::getUrl())
        ->assertOk();
});

it('disables the form without the `settings.*.update` permission', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageProspectConversionSettings::class)
        ->assertFormFieldDisabled('estimated_average_revenue');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageProspectConversionSettings::class)
        ->assertFormFieldEnabled('estimated_average_revenue');
});

it('hides the `save` action without the `settings.*.update` permission', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    livewire(ManageProspectConversionSettings::class)
        ->assertActionDoesNotExist(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));

    $user->givePermissionTo('settings.*.update');

    livewire(ManageProspectConversionSettings::class)
        ->assertActionVisible(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'));
});

it('requires proper permissions to update settings', function () {
    $user = User::factory()->licensed(Prospect::getLicenseType())->create();

    $user->givePermissionTo('settings.view-any');
    actingAs($user);

    $settings = app(ProspectConversionSettings::class);
    $settings->estimated_average_revenue = Money::parseByDecimal('100', 'USD');
    $settings->save();

    livewire(ManageProspectConversionSettings::class)
        ->fillForm([
            'estimated_average_revenue' => '200.00',
        ])
        ->call('save');

    expect($settings->estimated_average_revenue->getAmount())->toBe(Money::parseByDecimal('100', 'USD')->getAmount());

    $user->givePermissionTo('settings.*.update');

    livewire(ManageProspectConversionSettings::class)
        ->fillForm([
            'estimated_average_revenue' => '200.00',
        ])
        ->call('save');

    expect($settings->estimated_average_revenue->getAmount())->toBe(Money::parseByDecimal('200', 'USD')->getAmount());
});
