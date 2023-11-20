<?php

namespace Assist\Team\Database\Factories;

use Assist\Team\Models\Team;
use Assist\Division\Models\Division;
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

    public function configure(): TeamFactory|Factory
    {
        return $this->afterMaking(function (Team $team) {
            $team->division()->associate(fake()->randomElement([Division::inRandomOrder()->first(), null]));
        });
    }
}
