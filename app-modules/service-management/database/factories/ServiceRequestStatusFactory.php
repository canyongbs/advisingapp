<?php

namespace Assist\ServiceManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\ServiceManagement\Enums\ColumnColorOptions;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Enums\SystemServiceRequestClassification;

/**
 * @extends Factory<ServiceRequestStatus>
 */
class ServiceRequestStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classification' => $this->faker->randomElement(SystemServiceRequestClassification::cases()),
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(ColumnColorOptions::cases()),
        ];
    }

    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => SystemServiceRequestClassification::Open,
                'name' => 'Open',
                'color' => ColumnColorOptions::Success,
            ];
        });
    }

    public function in_progress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => SystemServiceRequestClassification::InProgress,
                'name' => 'In Progress',
                'color' => ColumnColorOptions::Info->value,
            ];
        });
    }

    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'classification' => SystemServiceRequestClassification::Closed,
                'name' => 'Closed',
                'color' => ColumnColorOptions::Warning->value,
            ];
        });
    }
}
