<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('local-password.minPasswordLength', 8);
        $this->migrator->add('local-password.maxPasswordLength', 64);
        $this->migrator->add('local-password.minUppercaseLetters', 1);
        $this->migrator->add('local-password.minLowercaseLetters', 1);
        $this->migrator->add('local-password.minDigits', 1);
        $this->migrator->add('local-password.minSpecialCharacters', 1);
        $this->migrator->add('local-password.numPreviousPasswords', 5);
        $this->migrator->add('local-password.maxPasswordAge', null);
        $this->migrator->add('local-password.blacklistCommonPasswords', false);
    }

    public function down(): void
    {
        $this->migrator->delete('local-password.minPasswordLength');
        $this->migrator->delete('local-password.maxPasswordLength');
        $this->migrator->delete('local-password.minUppercaseLetters');
        $this->migrator->delete('local-password.minLowercaseLetters');
        $this->migrator->delete('local-password.minDigits');
        $this->migrator->delete('local-password.minSpecialCharacters');
        $this->migrator->delete('local-password.numPreviousPasswords');
        $this->migrator->delete('local-password.maxPasswordAge');
        $this->migrator->delete('local-password.blacklistCommonPasswords');
    }
};
