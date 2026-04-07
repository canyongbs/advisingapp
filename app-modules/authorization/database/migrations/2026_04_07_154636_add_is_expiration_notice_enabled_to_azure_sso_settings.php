<?php

use App\Features\AzureExpirationNoticeFeature;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            $this->migrator->inGroup('azure_sso', function (SettingsBlueprint $blueprint): void {
                $blueprint->add('is_expiration_notice_enabled', false);
            });

        AzureExpirationNoticeFeature::activate();
        });
    }
    
    public function down(): void
    {
        AzureExpirationNoticeFeature::activate();
        
        DB::transaction(function () {
            $this->migrator->inGroup('azure_sso', function (SettingsBlueprint $blueprint): void {
                $blueprint->delete('is_expiration_notice_enabled');
            });
        });
    }
};
