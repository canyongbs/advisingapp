<?php

use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        try {
            $this->migrator->add('interaction_management.is_initiative_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_initiative_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_driver_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_driver_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_outcome_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_outcome_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_relation_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_relation_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_status_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_status_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_type_enabled', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('interaction_management.is_type_required', true);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
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
