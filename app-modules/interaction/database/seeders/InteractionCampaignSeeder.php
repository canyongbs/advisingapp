<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionCampaign;

class InteractionCampaignSeeder extends Seeder
{
    public function run(): void
    {
        InteractionCampaign::factory()
            ->count(10)
            ->create();
    }
}
