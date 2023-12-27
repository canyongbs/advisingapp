<?php

namespace AdvisingApp\InventoryManagement\Database\Factories;

use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\InventoryManagement\Models\MaintenanceProvider;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\MaintenanceActivity>
 */
class MaintenanceActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'maintenance_provider_id' => MaintenanceProvider::factory(),
            'date' => fake()->date(),
            'scheduled_date' => fake()->date(),
            'notes' => fake()->paragraph(),
        ];
    }

    public function inProgress(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::InProgress,
            ];
        });
    }

    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Completed,
            ];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Cancelled,
            ];
        });
    }

    public function delayed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Delayed,
            ];
        });
    }
}
