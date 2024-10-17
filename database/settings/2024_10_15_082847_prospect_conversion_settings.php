<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('prospect-conversion.estimated_average_revenue');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('prospect-conversion.estimated_average_revenue');
    }
};
