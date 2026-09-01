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

    $settings = app(CollegeBrandingSettings::class);

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

    $settings = app(CollegeBrandingSettings::class);

    expect($settings->college_text)->toBe('New College Text')
        ->and($settings->color)->toBe(Color::Red);
});
