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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AdvisingApp\Authorization\Tests\Tenant\Http\Controllers\RequestFactories\GenerateLoginOtpRequestFactory;
use App\Http\Middleware\CheckOlympusKey;
use App\Models\Authenticatable;
use App\Models\User;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

it('requires Olympus Key authentication', function () {
    post(
        route('otp-code.generate'),
        [
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'type' => Authenticatable::SUPER_ADMIN_ROLE,
        ]
    )
        ->assertForbidden();
});

it('can generate a login OTP for a non-existing user', function () {
    $email = fake()->safeEmail();
    $name = fake()->name();

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    // Verify that the user was created
    $user = User::where('email', $email)->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toEqual($name)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(OtpLoginCode::class, 1);

    $otpCode = OtpLoginCode::first();

    expect($otpCode->user_id)->toEqual($user->id);
});

it('can generate a login OTP for an existing user', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(OtpLoginCode::class, 1);

    $otpCode = OtpLoginCode::first();

    expect($otpCode->user_id)->toEqual($user->id);
});

it('can generate a login OTP for an existing user that is deleted', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    $user->delete();

    expect($user->trashed())->toBeTrue();

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->trashed())->toBeFalse()
        ->and($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(OtpLoginCode::class, 1);

    $otpCode = OtpLoginCode::first();

    expect($otpCode->user_id)->toEqual($user->id);
});

it('updates details of an existing user', function () {
    $user = User::factory()->create(
        [
            'is_external' => false,
            'email_verified_at' => null,
        ]
    );

    $email = $user->email;
    $name = fake()->name();

    expect($user->name)->not->toBe($name);

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(OtpLoginCode::class, 1);

    $otpCode = OtpLoginCode::first();

    expect($otpCode->user_id)->toEqual($user->id);
});

it('deletes existing OTP codes for a user', function () {
    $user = User::factory()->create();

    $email = $user->email;
    $name = $user->name;

    $existingOtpCode = OtpLoginCode::factory()->create(['user_id' => $user->id]);

    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    assertDatabaseCount(User::class, 1);

    $user->refresh();

    expect($user->name)->toEqual($name)
        ->and($user->email)->toEqual($email)
        ->and($user->is_external)->toBeTrue()
        ->and($user->hasExactRoles([Authenticatable::SUPER_ADMIN_ROLE]))->toBeTrue();

    assertDatabaseCount(OtpLoginCode::class, 1);

    expect($existingOtpCode->fresh())->toBeNull();

    $otpCode = OtpLoginCode::first();

    expect($otpCode->user_id)->toEqual($user->id);
});

it('returns a 6-digit OTP code', function () {
    $email = fake()->safeEmail();
    $name = fake()->name();

    $response = withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            [
                'email' => $email,
                'name' => $name,
                'type' => Authenticatable::SUPER_ADMIN_ROLE,
            ]
        )
        ->assertOk()
        ->assertJsonStructure(['link', 'otp']);

    $otp = $response->json('otp');

    expect($otp)->toBeInt()
        ->and($otp)->toBeGreaterThanOrEqual(100000)
        ->and($otp)->toBeLessThanOrEqual(999999);
});

it('requires valid data', function (GenerateLoginOtpRequestFactory $data, array $errors) {
    withoutMiddleware(CheckOlympusKey::class)
        ->post(
            route('otp-code.generate'),
            GenerateLoginOtpRequestFactory::new($data)->create()
        )
        ->assertInvalid($errors);

    assertDatabaseCount(OtpLoginCode::class, 0);
})
    ->with(
        [
            'email required' => [
                GenerateLoginOtpRequestFactory::new()->state(['email' => null]),
                ['email' => 'required'],
            ],
            'email must be valid email' => [
                GenerateLoginOtpRequestFactory::new()->state(['email' => 'invalid-email']),
                ['email' => 'email'],
            ],
            'name required' => [
                GenerateLoginOtpRequestFactory::new()->state(['name' => null]),
                ['name' => 'required'],
            ],
            'name string' => [
                GenerateLoginOtpRequestFactory::new()->state(['name' => 1]),
                ['name' => 'string'],
            ],
            'name max' => [
                GenerateLoginOtpRequestFactory::new()->state(['name' => str()->random(256)]),
                ['name' => ['The name may not be greater than 255 characters.']],
            ],
            'type required' => [
                GenerateLoginOtpRequestFactory::new()->state(['type' => null]),
                ['type' => 'required'],
            ],
            'type must be correct value' => [
                GenerateLoginOtpRequestFactory::new()->state(['type' => 'invalid']),
                ['type' => 'in'],
            ],
        ],
    );
