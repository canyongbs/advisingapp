<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Authorization\Models\OtpLoginCode;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

it('rejects an OTP code that is older than 20 minutes', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->create([
        'created_at' => now()->subMinutes(21),
    ]);

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects an OTP code that has already been used', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->used()->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects an incorrect OTP code', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->create();

    $panel = Filament::getPanel('admin');

    $wrongCode = $plainCode === 100000 ? 100001 : $plainCode - 1;

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $wrongCode,
    ])
        ->assertRedirect();

    assertGuest($panel->getAuthGuard());

    $otpCode->refresh();

    expect($otpCode->used_at)->toBeNull();
});

it('requires the code field', function () {
    $otpCode = OtpLoginCode::factory()->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [])
        ->assertInvalid(['code' => 'required']);

    assertGuest($panel->getAuthGuard());
});

it('requires the code to be exactly 6 digits', function (mixed $code) {
    $otpCode = OtpLoginCode::factory()->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => $code,
    ])
        ->assertInvalid(['code' => 'digits']);

    assertGuest($panel->getAuthGuard());
})
    ->with([
        'too short' => ['12345'],
        'too long' => ['1234567'],
        'non-numeric' => ['abcdef'],
    ]);

it('logs in the user and redirects to the admin panel home', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->create();

    $panel = Filament::getPanel('admin');

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());

    $otpCode->refresh();

    expect($otpCode->used_at)->not->toBeNull();
});

it('marks the OTP code as used after a successful login', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->create();

    expect($otpCode->used_at)->toBeNull();

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ]);

    $otpCode->refresh();

    expect($otpCode->used_at)->not->toBeNull();
});

it('does not allow a used OTP code to log in again', function () {
    $plainCode = random_int(100000, 999999);

    $otpCode = OtpLoginCode::factory()->withCode((string) $plainCode)->create();

    $panel = Filament::getPanel('admin');

    // First use — should succeed
    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());

    // Log out so we can test the second attempt clearly
    Auth::guard($panel->getAuthGuard())->logout();

    assertGuest($panel->getAuthGuard());

    // Second use — should be rejected
    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $plainCode,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});
