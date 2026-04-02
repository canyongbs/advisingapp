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
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\freezeTime;
use function Pest\Laravel\get;

it('requires a valid signed URL', function () {
    $otpCode = OtpLoginCode::factory()->create();

    get(route('otp-code.login', ['otpCode' => $otpCode->getKey()]))
        ->assertForbidden();
});

it('rejects an OTP code that is older than 20 minutes', function () {
    $otpCode = OtpLoginCode::factory()->create([
        'created_at' => now()->subMinutes(21),
    ]);

    get(URL::temporarySignedRoute(
        name: 'otp-code.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ))
        ->assertForbidden();
});

it('rejects an OTP code that has already been used', function () {
    $otpCode = OtpLoginCode::factory()->used()->create();

    get(URL::temporarySignedRoute(
        name: 'otp-code.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ))
        ->assertForbidden();
});

it('renders the OTP entry view for a valid unused OTP code', function () {
    $otpCode = OtpLoginCode::factory()->create();

    get(URL::temporarySignedRoute(
        name: 'otp-code.login',
        expiration: now()->addMinutes(10)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ))
        ->assertOk()
        ->assertViewIs('authorization::otp-entry')
        ->assertViewHas('verifyUrl');
});

it('rejects an OTP code that is exactly at the 20 minute boundary', function () {
    freezeTime(function () {
        $otpCode = OtpLoginCode::factory()->create([
            'created_at' => now()->subMinutes(20),
        ]);

        get(URL::temporarySignedRoute(
            name: 'otp-code.login',
            expiration: now()->addMinutes(20)->toImmutable(),
            parameters: [
                'otpCode' => $otpCode->getKey(),
            ],
        ))
            ->assertForbidden();
    });
});

it('allows an OTP code that is just under 20 minutes old', function () {
    freezeTime(function () {
        $otpCode = OtpLoginCode::factory()->create([
            'created_at' => now()->subMinutes(19)->subSeconds(59),
        ]);

        get(URL::temporarySignedRoute(
            name: 'otp-code.login',
            expiration: now()->addMinutes(20)->toImmutable(),
            parameters: [
                'otpCode' => $otpCode->getKey(),
            ],
        ))
            ->assertOk()
            ->assertViewIs('authorization::otp-entry');
    });
});

it('does not mark the OTP code as used when viewing the entry page', function () {
    $otpCode = OtpLoginCode::factory()->create();

    get(URL::temporarySignedRoute(
        name: 'otp-code.login',
        expiration: now()->addMinutes(20)->toImmutable(),
        parameters: [
            'otpCode' => $otpCode->getKey(),
        ],
    ))
        ->assertOk();

    $otpCode->refresh();

    expect($otpCode->used_at)->toBeNull();
});

