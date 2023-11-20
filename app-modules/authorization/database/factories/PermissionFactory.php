<?php

namespace Assist\Authorization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Authorization\Models\Permission>
 */
class PermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'guard_name' => fake()->randomElement(['web', 'api']),
        ];
    }
}
