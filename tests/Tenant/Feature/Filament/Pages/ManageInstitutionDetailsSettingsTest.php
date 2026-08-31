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

    expect(app(InstitutionDetailsSettings::class)->name)->toBe('Existing Institution Name');

    $user->givePermissionTo('settings.*.update');

    livewire(ManageInstitutionDetailsSettings::class)
        ->fillForm([
            'name' => 'New Institution Name',
        ])
        ->call('save');

    expect(app(InstitutionDetailsSettings::class)->name)->toBe('New Institution Name');
});
