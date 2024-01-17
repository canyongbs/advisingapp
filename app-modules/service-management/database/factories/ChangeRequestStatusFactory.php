<?php

namespace AdvisingApp\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\ServiceManagement\Enums\SystemChangeRequestClassification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\ServiceManagement\Models\ChangeRequestStatus>
 */
class ChangeRequestStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'classification' => fake()->randomElement(SystemChangeRequestClassification::cases()),
        ];
    }
}
