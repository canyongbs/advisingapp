<?php

namespace Assist\Engagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\EngagementDeliverable;

class EngagementSeeder extends Seeder
{
    public function run(): void
    {
        // For Student - deliver now
        Engagement::factory()
            ->count(10)
            ->has(EngagementDeliverable::factory()->deliverySuccessful()->count(1), 'engagementDeliverables')
            ->forStudent()
            ->deliverNow()
            ->create();

        // For Student - deliver later
        // Engagement::factory()
        //     ->count(7)
        //     ->has(EngagementDeliverable::factory()->count(1), 'engagementDeliverables')
        //     ->forStudent()
        //     ->deliverLater()
        //     ->create();

        // For Prospect - deliver now
        Engagement::factory()
            ->count(10)
            ->has(EngagementDeliverable::factory()->deliverySuccessful()->count(1), 'engagementDeliverables')
            ->forProspect()
            ->deliverNow()
            ->create();

        // For Prospect - deliver later
        // Engagement::factory()
        //     ->count(7)
        //     ->has(EngagementDeliverable::factory()->count(1), 'engagementDeliverables')
        //     ->forProspect()
        //     ->deliverLater()
        //     ->create();
    }
}
