<?php

namespace AdvisingApp\Analytics\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Analytics\Models\AnalyticsResource;

class AnalyticsResourceSeeder extends Seeder
{
    public function run(): void
    {
        AnalyticsResource::factory()
            ->count(25)
            ->create();
    }
}
