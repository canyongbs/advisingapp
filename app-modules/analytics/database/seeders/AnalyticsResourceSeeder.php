<?php

namespace AdvisingApp\Analytics\Database\Seeders;

use AdvisingApp\Analytics\Models\AnalyticsResource;
use Illuminate\Database\Seeder;

class AnalyticsResourceSeeder extends Seeder
{
    public function run(): void
    {
        AnalyticsResource::factory()
            ->count(25)
            ->create();
    }
}
