<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Enums\ColumnColorOptions;
use Assist\Case\Models\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

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
                'color' => ColumnColorOptions::SUCCESS->value,
            ];
        });
    }

    public function in_progress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'In Progress',
                'color' => ColumnColorOptions::INFO->value,
            ];
        });
    }

    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Closed',
                'color' => ColumnColorOptions::WARNING->value,
            ];
        });
    }
}
