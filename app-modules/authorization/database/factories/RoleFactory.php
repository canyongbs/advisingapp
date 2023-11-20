<?php

namespace Assist\Authorization\Database\Factories;

use Assist\Authorization\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->text(25),
            'guard_name' => fake()->randomElement(['web', 'api']),
        ];
    }
}
