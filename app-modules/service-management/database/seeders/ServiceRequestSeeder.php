<?php

namespace Assist\ServiceManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\ServiceManagement\Models\ServiceRequest;

class ServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequest::factory()
            ->count(30)
            ->create();
    }
}
