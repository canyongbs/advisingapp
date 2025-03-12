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

namespace AdvisingApp\Authorization\Settings;

use Spatie\LaravelSettings\Settings;

class LocalPasswordSettings extends Settings
{
    public const DEFAULT_MIN_PASSWORD_LENGTH = 8;

    public const DEFAULT_MAX_PASSWORD_LENGTH = 64;

    public const DEFAULT_MIN_UPPERCASE_LETTERS = 1;

    public const DEFAULT_MIN_LOWERCASE_LETTERS = 1;

    public const DEFAULT_MIN_DIGITS = 1;

    public const DEFAULT_MIN_SPECIAL_CHARACTERS = 1;

    public const DEFAULT_NUM_PREVIOUS_PASSWORDS = 5;

    public const DEFAULT_MAX_PASSWORD_AGE = null;

    public const DEFAULT_BLACKLIST_COMMON_PASSWORDS = true;

    public ?int $minPasswordLength = null;

    public ?int $maxPasswordLength = null;

    public ?int $minUppercaseLetters = null;

    public ?int $minLowercaseLetters = null;

    public ?int $minDigits = null;

    public ?int $minSpecialCharacters = null;

    public ?int $numPreviousPasswords = null;

    public ?int $maxPasswordAge = null;

    public bool $blacklistCommonPasswords = true;

    public static function group(): string
    {
        return 'local-password';
    }

    public function getMinPasswordLength(): int
    {
        return $this->minPasswordLength ?? self::DEFAULT_MIN_PASSWORD_LENGTH;
    }

    public function getMaxPasswordLength(): int
    {
        return $this->maxPasswordLength ?? self::DEFAULT_MAX_PASSWORD_LENGTH;
    }

    public function getMinUppercaseLetters(): int
    {
        return $this->minUppercaseLetters ?? self::DEFAULT_MIN_UPPERCASE_LETTERS;
    }

    public function getMinLowercaseLetters(): int
    {
        return $this->minLowercaseLetters ?? self::DEFAULT_MIN_LOWERCASE_LETTERS;
    }

    public function getMinDigits(): int
    {
        return $this->minDigits ?? self::DEFAULT_MIN_DIGITS;
    }

    public function getMinSpecialCharacters(): int
    {
        return $this->minSpecialCharacters ?? self::DEFAULT_MIN_SPECIAL_CHARACTERS;
    }

    public function getNumPreviousPasswords(): int
    {
        return $this->numPreviousPasswords ?? self::DEFAULT_NUM_PREVIOUS_PASSWORDS;
    }

    public function getMaxPasswordAge(): ?int
    {
        return $this->maxPasswordAge ?? self::DEFAULT_MAX_PASSWORD_AGE;
    }

    public function shouldBlacklistCommonPasswords(): bool
    {
        return $this->blacklistCommonPasswords ?? self::DEFAULT_BLACKLIST_COMMON_PASSWORDS;
    }
}
