<?php

namespace Assist\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\ServiceManagement\Models\ServiceRequestStatus;

class ServiceRequestStatusSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequestStatus::factory()
            ->open()
            ->create();

        ServiceRequestStatus::factory()
            ->in_progress()
            ->create();

        ServiceRequestStatus::factory()
            ->closed()
            ->create();
    }
}
