<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Authorization\Settings\LocalPasswordSettings;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Pest\Laravel\actingAs;

test('passwords can be validated based on the configured settings', function (string $password, ?string $expectedMessage = null) {
    $settings = app(LocalPasswordSettings::class);
    $settings->minPasswordLength = 12;
    $settings->maxPasswordLength = 48;
    $settings->minUppercaseLetters = 2;
    $settings->minLowercaseLetters = 2;
    $settings->minDigits = 2;
    $settings->minSpecialCharacters = 2;
    $settings->numPreviousPasswords = 3;
    $settings->blacklistCommonPasswords = true;
    $settings->save();

    $user = User::factory()
        ->create([
            'password' => Hash::make('aaAA11!!current'),
            'password_history' => [
                Hash::make('aaAA11!!previous1'),
                Hash::make('aaAA11!!previous2'),
                Hash::make('aaAA11!!previous3'),
            ],
        ]);

    actingAs($user);

    $validator = Validator::make(
        ['password' => $password],
        ['password' => [Password::default()]]
    );

    if ($expectedMessage) {
        expect($validator->fails())->toBeTrue();
        expect($validator->errors()->first('password'))->toBe($expectedMessage);
    } else {
        expect($validator->fails())->toBeFalse();
    }
})
    ->with([
        'valid' => ['aaAA11!!valid'],
        'min' => ['short', 'The password must be at least 12 characters.'],
        'max' => [str_repeat('a', 49), 'The password may not be greater than 48 characters.'],
        'uppercase' => ['aaa11!!valid', 'The password should contain at least 2 uppercase letters.'],
        'lowercase' => ['AAA11!!VALID', 'The password should contain at least 2 lowercase letters.'],
        'digits' => ['aaaAA!!valid', 'The password should contain at least 2 digits.'],
        'special' => ['aaaAA11valid', 'The password should contain at least 2 special characters.'],
        'current' => ['aaAA11!!current', 'The new password must be different from one you have used before.'],
        'previous1' => ['aaAA11!!previous1', 'The new password must be different from one you have used before.'],
        'previous2' => ['aaAA11!!previous2', 'The new password must be different from one you have used before.'],
        'previous3' => ['aaAA11!!previous3', 'The new password must be different from one you have used before.'],
        'blacklist' => ['g00dPa$$w0rD', 'The password is too common. Please choose a different password.'],
    ]);
