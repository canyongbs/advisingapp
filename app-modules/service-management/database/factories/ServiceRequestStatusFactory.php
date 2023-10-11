<?php

namespace Assist\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\ServiceManagement\Models\ServiceRequestStatus;

/**
 * @extends Factory<ServiceRequestStatus>
 */
class ServiceRequestStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(ColumnColorOptions::cases())->value,
        ];
    }

    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Open',
                'color' => ColumnColorOptions::Success->value,
            ];
        });
    }

    public function in_progress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'In Progress',
                'color' => ColumnColorOptions::Info->value,
            ];
        });
    }

    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Closed',
                'color' => ColumnColorOptions::Warning->value,
            ];
        });
    }
}
