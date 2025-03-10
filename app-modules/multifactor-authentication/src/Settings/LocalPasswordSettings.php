<?php

namespace AdvisingApp\MultifactorAuthentication\Settings;

use Spatie\LaravelSettings\Settings;

class LocalPasswordSettings extends Settings
{
    public const MIN_PASSWORD_LENGTH = 8;

    public const MAX_PASSWORD_LENGTH = 64;

    public const MIN_UPPERCASE_LETTERS = 1;

    public const MIN_LOWERCASE_LETTERS = 1;

    public const MIN_DIGITS = 1;

    public const MIN_SPECIAL_CHARACTERS = 1;

    public const NUM_PREVIOUS_PASSWORDS = 5;

    public const MAX_PASSWORD_AGE = null;

    public const BLACKLIST_COMMON_PASSWORDS = false;

    public ?int $minPasswordLength = null;

    public ?int $maxPasswordLength = null;

    public ?int $minUppercaseLetters = null;

    public ?int $minLowercaseLetters = null;

    public ?int $minDigits = null;

    public ?int $minSpecialCharacters = null;

    public ?int $numPreviousPasswords = null;

    public ?int $maxPasswordAge = null;

    public bool $blacklistCommonPasswords = false;

    public static function group(): string
    {
        return 'local-password';
    }

    public function getMinPasswordLength(): int
    {
        return $this->minPasswordLength ?? self::MIN_PASSWORD_LENGTH;
    }

    public function getMaxPasswordLength(): int
    {
        return $this->maxPasswordLength ?? self::MAX_PASSWORD_LENGTH;
    }

    public function getMinUppercaseLetters(): int
    {
        return $this->minUppercaseLetters ?? self::MIN_UPPERCASE_LETTERS;
    }

    public function getMinLowercaseLetters(): int
    {
        return $this->minLowercaseLetters ?? self::MIN_LOWERCASE_LETTERS;
    }

    public function getMinDigits(): int
    {
        return $this->minDigits ?? self::MIN_DIGITS;
    }

    public function getMinSpecialCharacters(): int
    {
        return $this->minSpecialCharacters ?? self::MIN_SPECIAL_CHARACTERS;
    }

    public function getNumPreviousPasswords(): int
    {
        return $this->numPreviousPasswords ?? self::NUM_PREVIOUS_PASSWORDS;
    }

    public function getMaxPasswordAge(): ?int
    {
        return $this->maxPasswordAge ?? self::MAX_PASSWORD_AGE;
    }

    public function shouldBlacklistCommonPasswords(): bool
    {
        return $this->blacklistCommonPasswords ?? self::BLACKLIST_COMMON_PASSWORDS;
    }
}
