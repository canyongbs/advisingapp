<?php

use Database\Migrations\Concerns\CanModifySettings;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class() extends SettingsMigration {
    use CanModifySettings;

    public function up(): void
    {
        $this->updateSettings(
            group: 'license',
            name: 'data',
            modifyPayload: function (array $data) {
                if (array_key_exists('serviceManagement', $data['addons'] ?? [])) {
                    $data['addons']['caseManagement'] = $data['addons']['serviceManagement'];
                    unset($data['addons']['serviceManagement']);
                }

                return $data;
            },
            isEncrypted: true,
        );
    }

    public function down(): void
    {
        $this->updateSettings(
            group: 'license',
            name: 'data',
            modifyPayload: function (array $data) {
                if (array_key_exists('caseManagement', $data['addons'] ?? [])) {
                    $data['addons']['serviceManagement'] = $data['addons']['caseManagement'];
                    unset($data['addons']['caseManagement']);
                }

                return $data;
            },
            isEncrypted: true,
        );
    }
};
