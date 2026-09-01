<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->migrator->deleteIfExists('notifications.name');
            $this->migrator->deleteIfExists('notifications.description');
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add('notifications.name');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add('notifications.description');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }
};
