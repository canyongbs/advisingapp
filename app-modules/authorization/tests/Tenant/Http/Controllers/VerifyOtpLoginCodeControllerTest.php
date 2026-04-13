<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
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
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;
use function Pest\Laravel\travelTo;

it('requires a valid signed URL', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    post(route('otp-code.verify', ['otpCode' => $otpCode->getKey()]), [
        'code' => (string) $code,
    ])
        ->assertForbidden();
});

it('rejects an expired signed URL', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    // Generate a valid signed URL while it's still within the 20-minute window
    $signedUrl = URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: $otpCode->created_at->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    );

    // Travel past the 20-minute expiration
    travelTo($otpCode->created_at->addMinutes(21));

    post($signedUrl, [
        'code' => (string) $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('rejects an OTP code that has already been used', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->used()->create();

    $panel = Filament::getPanel('admin');

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => (string) $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});

it('requires the code field', function () {
    $otpCode = OtpLoginCode::factory()->create();

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [])
        ->assertInvalid(['code' => 'required']);
});

it('requires the code to be exactly 6 digits', function (string $invalidCode, string $rule) {
    $otpCode = OtpLoginCode::factory()->create();

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => $invalidCode,
    ])
        ->assertInvalid(['code' => $rule]);
})
    ->with([
        'too short' => ['12345', 'digits'],
        'too long' => ['1234567', 'digits'],
        'non-numeric' => ['abcdef', 'digits'],
    ]);

it('returns an error when the OTP code is incorrect', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => '654321',
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['code']);

    assertGuest($panel->getAuthGuard());

    $otpCode->refresh();

    expect($otpCode->used_at)->toBeNull();
});

it('logs in the user and redirects to the admin panel home with a valid code', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => (string) $code,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());
});

it('marks the OTP code as used after successful verification', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    expect($otpCode->used_at)->toBeNull();

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => (string) $code,
    ])
        ->assertRedirect();

    $otpCode->refresh();

    expect($otpCode->used_at)->not->toBeNull();
});

it('does not mark the OTP code as used when the wrong code is entered', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    post(URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ), [
        'code' => '654321',
    ]);

    $otpCode->refresh();

    expect($otpCode->used_at)->toBeNull();
});

it('prevents reuse of an OTP code after successful verification', function () {
    $code = 123456;

    $otpCode = OtpLoginCode::factory()->withCode($code)->create();

    $panel = Filament::getPanel('admin');

    $signedUrl = URL::temporarySignedRoute(
        name: 'otp-code.verify',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    );

    // First verification should succeed
    post($signedUrl, [
        'code' => (string) $code,
    ])
        ->assertRedirect($panel->getHomeUrl());

    assertAuthenticatedAs($otpCode->user, $panel->getAuthGuard());

    // Log out to test reuse
    auth()->guard($panel->getAuthGuard())->logout();

    // Second verification should be rejected because used_at is now set
    post($signedUrl, [
        'code' => (string) $code,
    ])
        ->assertForbidden();

    assertGuest($panel->getAuthGuard());
});
