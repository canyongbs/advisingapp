<?php

namespace AdvisingApp\Alert\Database\Seeders;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\AlertStatus;
use Illuminate\Database\Seeder;

class AlertStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AlertStatus::factory()
            ->createMany(
                [
                    [
                        'name' => 'Active',
                        'classification' => SystemAlertStatusClassification::Active,
                        'order' => 1,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Resolved',
                        'classification' => SystemAlertStatusClassification::Resolved,
                        'order' => 2,
                        'is_default' => false
                    ],
                    [
                        'name' => 'Canceled',
                        'classification' => SystemAlertStatusClassification::Canceled,
                        'order' => 3,
                        'is_default' => false
                    ],
                ]
            );
    }
}
