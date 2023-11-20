<?php

namespace Assist\Campaign\Database\Factories;

use App\Models\User;
use Assist\CaseloadManagement\Models\Caseload;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Campaign\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'caseload_id' => Caseload::factory(),
            'name' => fake()->catchPhrase(),
            'enabled' => true,
        ];
    }

    public function disabled(): self
    {
        return $this->state([
            'enabled' => false,
        ]);
    }
}
