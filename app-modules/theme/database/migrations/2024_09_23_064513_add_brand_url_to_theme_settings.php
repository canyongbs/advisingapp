<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('theme.changelog_url');
        $this->migrator->add('theme.product_knowledge_base_url');
    }

    public function down(): void
    {
        $this->migrator->delete('theme.changelog_url');
        $this->migrator->delete('theme.product_knowledge_base_url');
    }
};
