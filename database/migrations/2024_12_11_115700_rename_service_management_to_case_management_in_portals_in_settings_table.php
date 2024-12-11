<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->rename('portal.resource_hub_portal_service_management', 'portal.resource_hub_portal_case_management');
    }

    public function down(): void
    {
        $this->migrator->rename('portal.resource_hub_portal_case_management', 'portal.resource_hub_portal_service_management');
    }
};
