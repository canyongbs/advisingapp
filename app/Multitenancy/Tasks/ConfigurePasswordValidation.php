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

namespace App\Multitenancy\Tasks;

use AdvisingApp\Authorization\Settings\LocalPasswordSettings;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class ConfigurePasswordValidation implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        Password::defaults(function (): Password {
            $settings = app(LocalPasswordSettings::class);

            return Password::min($settings->getMinPasswordLength())
                ->max($settings->getMaxPasswordLength())
                ->rules([
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        if (preg_match_all('/[A-Z]/', $value) < $settings->getMinUppercaseLetters()) {
                            $fail("The :attribute should contain at least {$settings->getMinUppercaseLetters()} uppercase " . Str::plural('letter', $settings->getMinUppercaseLetters()) . '.');
                        }
                    },
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        if (preg_match_all('/[a-z]/', $value) < $settings->getMinLowercaseLetters()) {
                            $fail("The :attribute should contain at least {$settings->getMinLowercaseLetters()} lowercase " . Str::plural('letter', $settings->getMinLowercaseLetters()) . '.');
                        }
                    },
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        if (preg_match_all('/[0-9]/', $value) < $settings->getMinDigits()) {
                            $fail("The :attribute should contain at least {$settings->getMinDigits()} " . Str::plural('digit', $settings->getMinDigits()) . '.');
                        }
                    },
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        if (preg_match_all('/[^A-Za-z0-9]/', $value) < $settings->getMinSpecialCharacters()) {
                            $fail("The :attribute should contain at least {$settings->getMinSpecialCharacters()} special " . Str::plural('character', $settings->getMinSpecialCharacters()) . '.');
                        }
                    },
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        $userToCheck = auth()->user();

                        if (! ($userToCheck instanceof User)) {
                            throw new Exception('User not found.');
                        }

                        if (filled($userToCheck->password) && Hash::check($value, $userToCheck->password)) {
                            $fail('The new :attribute must be different from one you have used before.');

                            return;
                        }

                        $numPreviousPasswords = $settings->getNumPreviousPasswords();

                        if (! $numPreviousPasswords) {
                            return;
                        }

                        $passwordHistory = $userToCheck->password_history ?? [];
                        $passwordHistory = array_slice($passwordHistory, -$numPreviousPasswords);

                        foreach ($passwordHistory as $oldPassword) {
                            if (Hash::check($value, $oldPassword)) {
                                $fail('The new :attribute must be different from one you have used before.');

                                return;
                            }
                        }
                    },
                    function (string $attribute, mixed $value, Closure $fail) use ($settings) {
                        if (! $settings->shouldBlacklistCommonPasswords()) {
                            return;
                        }

                        $file = fopen(resource_path('text/common-passwords.txt'), 'r');

                        if ($file === false) {
                            throw new Exception('Could not open the file of common passwords.');
                        }

                        while (($commonPassword = fgets($file)) !== false) {
                            if (Str::lower(trim($commonPassword)) === Str::lower(trim($value))) {
                                fclose($file);

                                $fail('The :attribute is too common. Please choose a different password.');

                                return;
                            }
                        }
                    },
                ]);
        });
    }

    public function forgetCurrent(): void
    {
        Password::defaults(Password::min(8));
    }
}
