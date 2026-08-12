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

use AdvisingApp\Authorization\Filament\Pages\Auth\ConfirmOneTimeLoginCode;
use App\Models\User;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

it('stashes the user in session and redirects to the code challenge', function () {
    $user = User::factory()->create(['password' => null]);

    get(URL::signedRoute('login.one-time', ['user' => $user], absolute: false))
        ->assertRedirect(route('filament.admin.auth.one-time-login'))
        ->assertSessionHas(ConfirmOneTimeLoginCode::SESSION_KEY . '.user', $user->getKey());

    assertGuest();
});

it('forbids an unsigned link', function () {
    $user = User::factory()->create(['password' => null]);
    get(route('login.one-time', ['user' => $user]))->assertForbidden();
    assertGuest();
});

it('forbids an internal user that already has a password', function () {
    $user = User::factory()->create();
    get(URL::signedRoute('login.one-time', ['user' => $user], absolute: false))->assertForbidden();
    assertGuest();
});

it('allows an external user that already has a password to reuse the link', function () {
    $user = User::factory()->external()->create();

    get(URL::signedRoute('login.one-time', ['user' => $user], absolute: false))
        ->assertRedirect(route('filament.admin.auth.one-time-login'))
        ->assertSessionHas(ConfirmOneTimeLoginCode::SESSION_KEY . '.user', $user->getKey());

    assertGuest();
});
