<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('multifactor.required', false);
    }

    public function down(): void
    {
        $this->migrator->delete('multifactor.required');
    }
};
