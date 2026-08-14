<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            $this->migrator->add('notifications.name');
            $this->migrator->add('notifications.from_name');
            $this->migrator->add('notifications.description');
            $this->migrator->add('notifications.logo');
            $this->migrator->add('notifications.primary_color');
        });        
    }

    public function down(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('notifications.name');
            $this->migrator->deleteIfExists('notifications.from_name');
            $this->migrator->deleteIfExists('notifications.description');
            $this->migrator->deleteIfExists('notifications.logo');
            $this->migrator->deleteIfExists('notifications.primary_color');
        });
    }
};
