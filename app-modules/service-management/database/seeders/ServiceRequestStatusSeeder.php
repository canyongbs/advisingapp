<?php

namespace Assist\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\ServiceManagement\Models\ServiceRequestStatus;

class ServiceRequestStatusSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestStatus::factory()
            ->createMany(
                [
                    ['name' => 'New', 'color' => ColumnColorOptions::Info],
                    ['name' => 'In-Progress', 'color' => ColumnColorOptions::Info],
                    ['name' => 'Pending College', 'color' => ColumnColorOptions::Warning],
                    ['name' => 'Pending District', 'color' => ColumnColorOptions::Danger],
                    ['name' => 'Pending Student', 'color' => ColumnColorOptions::Gray],
                ]
            );
    }
}
