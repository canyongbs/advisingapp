<?php

namespace Assist\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

/**
 * @extends Factory<ServiceRequestPriority>
 */
class ServiceRequestPriorityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'order' => $this->faker->randomNumber(1),
        ];
    }

    public function high(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'High',
                'order' => 1,
            ];
        });
    }

    public function medium(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Medium',
                'order' => 2,
            ];
        });
    }

    public function low(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Low',
                'order' => 3,
            ];
        });
    }
}
