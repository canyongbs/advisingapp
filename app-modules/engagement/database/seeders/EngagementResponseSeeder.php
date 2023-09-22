<?php

namespace Assist\Engagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Engagement\Models\EngagementResponse;

class EngagementResponseSeeder extends Seeder
{
    public function run(): void
    {
        EngagementResponse::factory()
            ->count(50)
            ->create();
    }
}
