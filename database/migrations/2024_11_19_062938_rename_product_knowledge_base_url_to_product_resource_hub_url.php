<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->rename('theme.product_knowledge_base_url', 'theme.product_resource_hub_url');
    }

    public function down(): void
    {
        $this->migrator->rename('theme.product_resource_hub_url', 'theme.product_knowledge_base_url');
    }
};
