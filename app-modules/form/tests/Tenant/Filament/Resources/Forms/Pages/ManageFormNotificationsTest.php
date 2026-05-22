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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Resources\Forms\Pages\ManageFormNotifications;
use AdvisingApp\Form\Models\Form;
use App\Models\User;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('can save notification users to a form', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $userToNotify = User::factory()->licensed(LicenseType::cases())->create();

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm([
            'notification_users' => [$userToNotify->getKey()],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->fresh()->notificationUsers)->toHaveCount(1);
    expect($form->fresh()->notificationUsers->first()->id)->toBe($userToNotify->id);
});

test('can enable notify_via_email and it persists', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['notify_via_email' => false]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm(['notify_via_email' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->fresh()->notify_via_email)->toBeTrue();
});

test('can enable notify_via_app and it persists', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['notify_via_app' => false]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm(['notify_via_app' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->fresh()->notify_via_app)->toBeTrue();
});

test('notify_to_care_team toggle is only visible when form is authenticated', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => false]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->assertFormFieldIsHidden('notify_to_care_team');

    $form->update(['is_authenticated' => true]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->assertFormFieldIsVisible('notify_to_care_team');
});

test('notify_to_subscibers toggle is only visible when form is authenticated', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => false]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->assertFormFieldIsHidden('notify_to_subscibers');

    $form->update(['is_authenticated' => true]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->assertFormFieldIsVisible('notify_to_subscibers');
});

test('can enable notify_to_care_team on an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create([
        'is_authenticated' => true,
        'notify_to_care_team' => false,
    ]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm(['notify_to_care_team' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->fresh()->notify_to_care_team)->toBeTrue();
});

test('can enable notify_to_subscibers on an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create([
        'is_authenticated' => true,
        'notify_to_subscibers' => false,
    ]);

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm(['notify_to_subscibers' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($form->fresh()->notify_to_subscibers)->toBeTrue();
});

test('can save all notification settings together on an authenticated form', function () {
    asSuperAdmin();

    $form = Form::factory()->create(['is_authenticated' => true]);
    $userToNotify = User::factory()->licensed(LicenseType::cases())->create();

    livewire(ManageFormNotifications::class, ['record' => $form->getKey()])
        ->fillForm([
            'notification_users' => [$userToNotify->getKey()],
            'notify_to_care_team' => true,
            'notify_to_subscibers' => true,
            'notify_via_app' => true,
            'notify_via_email' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $form->fresh();

    expect($fresh->notify_to_care_team)->toBeTrue();
    expect($fresh->notify_to_subscibers)->toBeTrue();
    expect($fresh->notify_via_app)->toBeTrue();
    expect($fresh->notify_via_email)->toBeTrue();
    expect($fresh->notificationUsers)->toHaveCount(1);
});
