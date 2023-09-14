<?php

namespace Assist\AssistDataModel\Database\Factories;

use Assist\AssistDataModel\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => $this->faker->randomNumber(9),
            'acad_career' => $this->faker->randomElement(['NC', 'CRED']),
            'division' => $this->faker->randomElement(['ABC01', 'ABD02', 'ABE03']),
            'semester' => $this->faker->randomNumber(4),
            'class_nbr' => $this->faker->randomNumber(5),
            'subject' => $this->faker->randomElement(['ACC', 'FITNESS', 'MATH']),
            'catalog_nbr' => $this->faker->randomNumber(3) . '-' . $this->faker->randomNumber(5),
            'enrl_status' => $this->faker->randomElement(['DROP', 'ENRL']),
            'enrl_add_dt' => $this->faker->dateTime(),
            'enrl_drop_dt' => $this->faker->dateTime(),
            'crse_grade_off' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'W']),
            'unt_taken' => $this->faker->randomNumber(1),
            'unt_earned' => $this->faker->randomNumber(1),
            'last_upd_dt_stmp' => $this->faker->dateTime(),
        ];
    }
}
