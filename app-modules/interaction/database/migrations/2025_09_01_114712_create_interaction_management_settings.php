<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('interaction_management.is_initiative_enabled', true);
        $this->migrator->add('interaction_management.is_initiative_required', true);

        $this->migrator->add('interaction_management.is_driver_enabled', true);
        $this->migrator->add('interaction_management.is_driver_required', true);

        $this->migrator->add('interaction_management.is_outcome_enabled', true);
        $this->migrator->add('interaction_management.is_outcome_required', true);

        $this->migrator->add('interaction_management.is_relation_enabled', true);
        $this->migrator->add('interaction_management.is_relation_required', true);

        $this->migrator->add('interaction_management.is_status_enabled', true);
        $this->migrator->add('interaction_management.is_status_required', true);

        $this->migrator->add('interaction_management.is_type_enabled', true);
        $this->migrator->add('interaction_management.is_type_required', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('interaction_management.is_initiative_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_initiative_required');

        $this->migrator->deleteIfExists('interaction_management.is_driver_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_driver_required');

        $this->migrator->deleteIfExists('interaction_management.is_outcome_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_outcome_required');

        $this->migrator->deleteIfExists('interaction_management.is_relation_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_relation_required');

        $this->migrator->deleteIfExists('interaction_management.is_status_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_status_required');

        $this->migrator->deleteIfExists('interaction_management.is_type_enabled');
        $this->migrator->deleteIfExists('interaction_management.is_type_required');
    }
};
