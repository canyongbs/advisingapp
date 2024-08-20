<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('theme.is_enabled', false);
        $this->migrator->add('theme.college_text');
        $this->migrator->add('theme.color');
    }
};
