<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('ai.reasoning_effort');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('ai.reasoning_effort');
    }
};