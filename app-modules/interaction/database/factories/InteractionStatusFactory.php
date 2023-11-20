<?php

namespace Assist\Interaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Enums\ColumnColorOptions;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Interaction\Models\InteractionStatus>
 */
class InteractionStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'color' => $this->faker->randomElement(ColumnColorOptions::cases())->value,
        ];
    }
}
