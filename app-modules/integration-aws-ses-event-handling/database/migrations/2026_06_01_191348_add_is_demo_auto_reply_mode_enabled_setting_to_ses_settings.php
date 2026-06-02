<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends SettingsMigration
{
    public function up(): void
    {
        try {
            $this->migrator->inGroup('ses', function (SettingsBlueprint $blueprint): void {
                $blueprint->add('is_demo_auto_reply_mode_enabled', false);
            });
        } catch (SettingAlreadyExists) {
            
        }
    }

    public function down(): void
    {
        $this->migrator->inGroup('ses', function (SettingsBlueprint $blueprint): void {
            $blueprint->delete('is_demo_auto_reply_mode_enabled');
        });
    }
};
