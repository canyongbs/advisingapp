<?php

namespace Assist\Authorization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Authorization\Models\Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'guard_name' => fake()->randomElement(['web', 'api']),
        ];
    }
}
