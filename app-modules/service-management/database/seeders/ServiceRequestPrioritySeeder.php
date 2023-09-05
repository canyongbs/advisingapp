<?php

namespace Assist\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

class ServiceRequestPrioritySeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestPriority::factory()
            ->count(3)
            ->sequence(
                ['name' => 'High', 'order' => 1],
                ['name' => 'Medium', 'order' => 2],
                ['name' => 'Low', 'order' => 3],
            )
            ->create();
    }
}
