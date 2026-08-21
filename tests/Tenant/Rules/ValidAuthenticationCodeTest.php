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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Models\ApplicationAuthentication;
use App\Rules\ValidAuthenticationCode;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

function validateAuthenticationCode(ApplicationAuthentication $authentication, string $code): ValidatorInstance
{
    return Validator::make(
        ['code' => $code],
        ['code' => [new ValidAuthenticationCode($authentication)]],
    );
}

it('passes for the correct code', function () {
    $authentication = ApplicationAuthentication::factory()->create(['code' => Hash::make('123456')]);

    expect(validateAuthenticationCode($authentication, '123456')->passes())->toBeTrue();
});

it('fails for an incorrect code', function () {
    $authentication = ApplicationAuthentication::factory()->create(['code' => Hash::make('123456')]);

    $validator = validateAuthenticationCode($authentication, '654321');

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('code'))->toBe('The provided code is invalid.');
});

it('locks the record after the maximum failed attempts and then rejects even the correct code', function () {
    $authentication = ApplicationAuthentication::factory()->create(['code' => Hash::make('123456')]);

    foreach (range(1, AuthenticationCodeRateLimiter::MAX_ATTEMPTS) as $attempt) {
        expect(validateAuthenticationCode($authentication, '654321')->passes())->toBeFalse();
    }

    $validator = validateAuthenticationCode($authentication, '123456');

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('code'))->toBe('Too many invalid attempts. Please request a new code.');
});

it('clears the failed-attempt counter after a successful validation', function () {
    $authentication = ApplicationAuthentication::factory()->create(['code' => Hash::make('123456')]);

    foreach (range(1, AuthenticationCodeRateLimiter::MAX_ATTEMPTS - 1) as $attempt) {
        validateAuthenticationCode($authentication, '654321');
    }

    expect(validateAuthenticationCode($authentication, '123456')->passes())->toBeTrue();

    $validator = validateAuthenticationCode($authentication, '654321');

    expect($validator->errors()->first('code'))->toBe('The provided code is invalid.');
});
