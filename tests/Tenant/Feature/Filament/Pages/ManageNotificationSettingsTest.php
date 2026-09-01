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

use App\Filament\Pages\ManageNotificationSettings;
use App\Models\User;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the manage notification settings page', function () {
    asSuperAdmin();

    get(ManageNotificationSettings::getUrl())
        ->assertOk();
});

it('loads existing data into the form', function () {
    asSuperAdmin();

    $settings = app(NotificationSettings::class);
    $settings->from_name = 'Existing From Name';
    $settings->save();

    livewire(ManageNotificationSettings::class)
        ->assertSchemaStateSet(['from_name' => 'Existing From Name']);
});

it('can update the notification settings', function () {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm([
            'from_name' => 'New From Name',
            'primary_color' => Color::Blue->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(NotificationSettings::class);

    expect($settings->from_name)->toBe('New From Name')
        ->and($settings->primary_color)->toBe(Color::Blue);
});

it('validates the inputs', function (array $state, array $errors) {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm($state)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'from_name max' => [['from_name' => str_repeat('a', 151)], ['from_name' => 'max']],
]);

describe('authorization', function () {
    it('requires proper permissions to access', function () {
        $user = User::factory()->create();

        actingAs($user);

        get(ManageNotificationSettings::getUrl())
            ->assertForbidden();

        $user->givePermissionTo('settings.view-any');

        get(ManageNotificationSettings::getUrl())
            ->assertOk();
    });

    it('requires proper permissions to update settings', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $settings = app(NotificationSettings::class);

        livewire(ManageNotificationSettings::class)
            ->fillForm([
                'from_name' => 'New From Name',
                'primary_color' => Color::Blue->value,
            ])
            ->call('save');

        expect($settings->from_name)->not()->toBe('New From Name')
            ->and($settings->primary_color)->not()->toBe(Color::Blue);

        $user->givePermissionTo('settings.*.update');

        livewire(ManageNotificationSettings::class)
            ->fillForm([
                'from_name' => 'New From Name',
                'primary_color' => Color::Blue->value,
            ])
            ->call('save');

        expect($settings->from_name)->toBe('New From Name')
            ->and($settings->primary_color)->toBe(Color::Blue);
    });
});
