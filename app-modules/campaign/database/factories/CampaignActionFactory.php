<?php

namespace Assist\Campaign\Database\Factories;

use Assist\Campaign\Models\Campaign;
use Assist\Campaign\Enums\CampaignActionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Campaign\Models\CampaignAction>
 */
class CampaignActionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'type' => fake()->randomElement([
                CampaignActionType::BulkEngagement,
            ]),
            'data' => [],
            'executed_at' => fake()->dateTimeBetween('-1 week', '+1 year'),
        ];
    }
}
