<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\ServiceRequest;

class ServiceRequestSeeder extends Seeder
{
    public function run(): void
    {
        ServiceRequest::factory()
            ->count(30)
            ->create();
    }
}
