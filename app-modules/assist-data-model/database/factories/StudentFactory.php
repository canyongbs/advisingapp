<?php

namespace Assist\AssistDataModel\Database\Factories;

use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sisid' => $this->faker->randomNumber(9),
            'otherid' => $this->faker->randomNumber(9),
            'first' => $this->faker->firstName(),
            'last' => $this->faker->lastName(),
            'full' => $this->faker->name(),
            'preferred' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'email_2' => $this->faker->email(),
            'mobile' => $this->faker->phoneNumber(),
            'sms_opt_out' => $this->faker->randomElement(['Y', 'N']),
            'email_bounce' => $this->faker->randomElement(['Y', 'N']),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'address_2' => $this->faker->address(),
            'birthdate' => $this->faker->date(),
            'hsgrad' => $this->faker->year(),
            'dual' => $this->faker->randomElement(['Y', 'N']),
            'ferpa' => $this->faker->randomElement(['Y', 'N']),
            'gpa' => $this->faker->randomFloat(3, 0, 4),
            'dfw' => $this->faker->randomElement(['Y', 'N']),
            'firstgen' => $this->faker->randomElement(['Y', 'N']),
            'ethnicity' => $this->faker->randomElement(['White', 'Black', 'Hispanic', 'Asian', 'Other']),
            'lastlmslogin' => $this->faker->dateTime(),
        ];
    }
}
