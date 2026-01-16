<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends SettingsMigration
{
    public function up(): void
    {
        try {
            $this->migrator->add('institution.ipeds_id', '');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('institution.name', '');
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('institution.dark_logo', null);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }

        try {
            $this->migrator->add('institution.light_logo', null);
        } catch (SettingAlreadyExists $exception) {
            // do nothing
        }
    }
    public function down(): void
    {
        $this->migrator->deleteIfExists('institution.ipeds_id');
        $this->migrator->deleteIfExists('institution.name');
        $this->migrator->deleteIfExists('institution.dark_logo');
        $this->migrator->deleteIfExists('institution.light_logo');
    }
};
