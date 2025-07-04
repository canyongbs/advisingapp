<?php

namespace Database\Factories;

use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SystemUser>
 */
class SystemUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
