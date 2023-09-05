<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\ServiceRequestStatus;

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
