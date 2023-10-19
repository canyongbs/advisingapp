<?php

namespace Assist\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;

class ServiceRequestStatusSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestStatus::factory()
            ->createMany(
                [
                    [
                        'classification' => SystemServiceRequestClassification::Open,
                        'name' => 'New',
                        'color' => ColumnColorOptions::Info,
                    ],
                    [
                        'classification' => SystemServiceRequestClassification::InProgress,
                        'name' => 'In-Progress',
                        'color' => ColumnColorOptions::Info,
                    ],
                    [
                        'classification' => SystemServiceRequestClassification::Custom,
                        'name' => 'Pending College',
                        'color' => ColumnColorOptions::Warning,
                    ],
                    [
                        'classification' => SystemServiceRequestClassification::Custom,
                        'name' => 'Pending District',
                        'color' => ColumnColorOptions::Danger],
                    [
                        'classification' => SystemServiceRequestClassification::Custom,
                        'name' => 'Pending Student',
                        'color' => ColumnColorOptions::Gray],
                ]
            );
    }
}
