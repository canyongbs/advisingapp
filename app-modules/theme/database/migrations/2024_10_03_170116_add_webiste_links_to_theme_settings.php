<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
      $this->migrator->add('theme.is_support_url_enabled', false);
      $this->migrator->add('theme.is_recent_updates_url_enabled', false);
      $this->migrator->add('theme.is_custom_link_url_enabled', false);
      $this->migrator->add('theme.support_url');
      $this->migrator->add('theme.recent_updates_url');
      $this->migrator->add('theme.custom_link_label');
      $this->migrator->add('theme.custom_link_url');
    }

    public function down(): void
    {
      $this->migrator->delete('theme.is_support_url_enabled');
      $this->migrator->delete('theme.is_recent_updates_url_enabled');
      $this->migrator->delete('theme.is_custom_link_url_enabled');
      $this->migrator->delete('theme.support_url');
      $this->migrator->delete('theme.recent_updates_url');
      $this->migrator->delete('theme.custom_link_label');
      $this->migrator->delete('theme.custom_link_url');
    }
};
