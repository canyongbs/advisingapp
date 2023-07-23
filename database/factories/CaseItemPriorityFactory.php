<?php

namespace Database\Factories;

use App\Models\CaseItemPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseItemPriority>
 */
class CaseItemPriorityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'order' => $this->faker->randomNumber(1),
        ];
    }
}
