<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => $this->faker->randomNumber(9),
            'first_name' => 'test',
            'middle_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.com',
        ];
    }
}
