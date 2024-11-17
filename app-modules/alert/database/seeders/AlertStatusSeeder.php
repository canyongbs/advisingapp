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
                        'sort' => 1,
                        'is_default' => true
                    ],
                    [
                        'name' => 'Resolved',
                        'classification' => SystemAlertStatusClassification::Resolved,
                        'sort' => 2,
                        'is_default' => false
                    ],
                    [
                        'name' => 'Canceled',
                        'classification' => SystemAlertStatusClassification::Canceled,
                        'sort' => 3,
                        'is_default' => false
                    ],
                ]
            );
    }
}
