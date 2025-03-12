<?php

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
