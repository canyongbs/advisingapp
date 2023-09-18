<?php

namespace Assist\Division\Database\Factories;

use App\Models\User;
use Assist\Division\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'code' => fake()->unique()->word(),
            'header' => fake()->words(asText: true),
            'footer' => fake()->words(asText: true),
        ];
    }

    public function configure(): DivisionFactory|Factory
    {
        return $this->afterMaking(function (Division $division) {
            $user = User::inRandomOrder()->first() ?? User::factory()->create();

            $division->createdBy()->associate($user);
            $division->updatedBy()->associate($user);
        });
    }
}
