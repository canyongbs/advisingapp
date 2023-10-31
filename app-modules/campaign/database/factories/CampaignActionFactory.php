<?php

namespace Assist\Campaign\Database\Factories;

use Carbon\Carbon;
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
            'execute_at' => fake()->dateTimeBetween('+1 week', '+1 year'),
        ];
    }

    public function successfulExecution(?Carbon $at = null): self
    {
        return $this->state([
            'execute_at' => $at ?? now(),
            'last_execution_attempt_at' => $at ?? now(),
            'successfully_executed_at' => $at ?? now(),
        ]);
    }

    public function failedExecution(?Carbon $at = null): self
    {
        return $this->state([
            'execute_at' => $at ?? now(),
            'last_execution_attempt_at' => $at ?? now(),
            'last_execution_attempt_error' => fake()->sentence(),
        ]);
    }
}
