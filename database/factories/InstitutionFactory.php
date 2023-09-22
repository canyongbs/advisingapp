<?php

namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->word(),
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
        ];
    }
}
