<?php

namespace Assist\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Models\ServiceRequestType;

/**
 * @extends Factory<ServiceRequestType>
 */
class ServiceRequestTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
