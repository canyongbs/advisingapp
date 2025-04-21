<?php

namespace AdvisingApp\Engagement\Database\Seeders;

use AdvisingApp\Engagement\Models\EngagementResponse;
use Illuminate\Database\Seeder;

class EngagementResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EngagementResponse::factory()->count(10)->create();
    }
}
