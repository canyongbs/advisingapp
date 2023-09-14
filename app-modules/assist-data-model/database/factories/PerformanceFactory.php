<?php

namespace Assist\AssistDataModel\Database\Factories;

use Assist\AssistDataModel\Models\Performance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Performance>
 */
class PerformanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => $this->faker->randomNumber(9),
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'first_gen' => $this->faker->boolean(),
            'cum_att' => $this->faker->randomNumber(2),
            'cum_ern' => $this->faker->randomNumber(2),
            'pct_ern' => 0,
            'cum_gpa' => $this->faker->randomFloat(3, 0, 4),
            'max_dt' => $this->faker->dateTime(),
        ];
    }
}
