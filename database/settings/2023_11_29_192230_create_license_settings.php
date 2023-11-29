<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        // TODO: When we eventually have a paradigm for validating and retrieving license data, change this to both default to null, nothing passed into the second argument

        $this->migrator->addEncrypted('license.license_key', 'ABCD-1234-EFGH-5678');
        $this->migrator->addEncrypted(
            'license.data',
            [
                'updated_at' => now(),
                'subscription' => [
                    'client_name' => 'Jane Smith',
                    'partner_name' => 'Fake Edu Tech',
                    'client_po' => 'abc123',
                    'partner_po' => 'def456',
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                ],
                'limits' => [
                    'crm_seats' => 30,
                    'analytics_seats' => 15,
                    'emails' => 1000,
                    'sms' => 1000,
                    'reset_date' => now()->format('m-d'),
                ],
                'addons' => [
                    'online_admissions' => true,
                    'realtime_chat' => true,
                    'dynamic_forms' => true,
                    'conduct_surveys' => true,
                    'personal_assistant' => true,
                    'service_management' => true,
                    'knowledge_management' => true,
                    'student_and_prospect_portal' => true,
                ],
            ]
        );
    }
};
