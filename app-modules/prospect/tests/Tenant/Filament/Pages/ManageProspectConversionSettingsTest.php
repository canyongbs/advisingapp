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
