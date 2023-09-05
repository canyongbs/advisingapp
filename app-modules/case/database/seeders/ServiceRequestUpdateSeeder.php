<?php

namespace Assist\Case\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Case\Models\ServiceRequest;
use Assist\Case\Models\ServiceRequestUpdate;

class ServiceRequestUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceRequest::each(function (ServiceRequest $serviceRequest) {
            ServiceRequestUpdate::factory()
                ->count(3)
                ->for($serviceRequest, 'serviceRequest')
                ->create();
        });
    }
}
