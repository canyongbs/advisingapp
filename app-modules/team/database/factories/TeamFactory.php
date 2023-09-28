<?php

namespace Assist\Team\Database\Factories;

use Assist\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(asText: true),
            'description' => fake()->sentence(),
        ];
    }
}
