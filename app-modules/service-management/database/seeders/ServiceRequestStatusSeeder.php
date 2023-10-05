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
                    ['name' => 'New', 'color' => ColumnColorOptions::INFO],
                    ['name' => 'In-Progress', 'color' => ColumnColorOptions::INFO],
                    ['name' => 'Pending College', 'color' => ColumnColorOptions::WARNING],
                    ['name' => 'Pending District', 'color' => ColumnColorOptions::DANGER],
                    ['name' => 'Pending Student', 'color' => ColumnColorOptions::GRAY],
                ]
            );
    }
}
