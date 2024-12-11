<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->rename('portal.knowledge_management_portal_enabled', 'portal.resource_hub_portal_enabled');
        $this->migrator->rename('portal.knowledge_management_portal_service_management', 'portal.resource_hub_portal_service_management');
        $this->migrator->rename('portal.knowledge_management_portal_primary_color', 'portal.resource_hub_portal_primary_color');
        $this->migrator->rename('portal.knowledge_management_portal_rounding', 'portal.resource_hub_portal_rounding');
        $this->migrator->rename('portal.knowledge_management_portal_authorized_domain', 'portal.resource_hub_portal_authorized_domain');
    }

    public function down(): void
    {
        $this->migrator->rename('portal.resource_hub_portal_enabled', 'portal.knowledge_management_portal_enabled');
        $this->migrator->rename('portal.resource_hub_portal_service_management', 'portal.knowledge_management_portal_service_management');
        $this->migrator->rename('portal.resource_hub_portal_primary_color', 'portal.knowledge_management_portal_primary_color');
        $this->migrator->rename('portal.resource_hub_portal_rounding', 'portal.knowledge_management_portal_rounding');
        $this->migrator->rename('portal.resource_hub_portal_authorized_domain', 'portal.knowledge_management_portal_authorized_domain');
    }
};
