<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_id' => $this->faker->randomNumber(9),
            'name' => $this->faker->word(),
        ];
    }
}
