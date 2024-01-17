<?php

namespace AdvisingApp\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\ServiceManagement\Models\ChangeRequestType>
 */
class ChangeRequestTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
