<?php

namespace Assist\AssistDataModel\Database\Factories;

use Assist\AssistDataModel\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => $this->faker->randomNumber(9),
            'otherid' => $this->faker->randomNumber(9),
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'acad_plan' => $this->faker->randomElement(['NONCREDIT', $this->faker->randomNumber(4)]),
            'prog_status' => 'AC',
            'cum_gpa' => $this->faker->randomFloat(3, 0, 4),
            'semester' => $this->faker->randomNumber(4),
            'descr' => $this->faker->words(2),
            'foi' => 'FOI ' . $this->faker->words(),
            'change_dt' => $this->faker->dateTime(),
            'declare_dt' => $this->faker->dateTime(),
        ];
    }
}
