<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('stock-media.active', false);

        $this->migrator->add('stock-media.provider');

        $this->migrator->addEncrypted('stock-media.pexels_api_key');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('stock-media.active');

        $this->migrator->deleteIfExists('stock-media.provider');

        $this->migrator->deleteIfExists('stock-media.pexels_api_key');
    }
};
