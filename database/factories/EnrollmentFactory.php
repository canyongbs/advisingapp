<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'enrollment_id' => $this->faker->randomNumber(9),
            'status' => $this->faker->randomElement(['enrolled', 'dropped', 'passed']),
        ];
    }
}
