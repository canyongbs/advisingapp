<?php

namespace AdvisingApp\InventoryManagement\Database\Factories;

use Database\Factories\Concerns\RandomizeState;
use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\InventoryManagement\Models\MaintenanceProvider;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\MaintenanceActivity>
 */
class MaintenanceActivityFactory extends Factory
{
    use RandomizeState;

    public function definition(): array
    {
        $date = fake()->date();

        return [
            'asset_id' => Asset::factory(),
            'completed_date' => $date,
            'details' => fake()->sentence(),
            'maintenance_provider_id' => MaintenanceProvider::factory(),
            'notes' => fake()->paragraph(),
            'scheduled_date' => $date,
        ];
    }

    public function inProgress(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::InProgress,
                'completed_date' => null,
                'scheduled_date' => now(),
            ];
        });
    }

    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Completed,
                'completed_date' => now(),
                'scheduled_date' => now(),
            ];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Cancelled,
                'completed_date' => null,
                'scheduled_date' => now()->subDays(7),
            ];
        });
    }

    public function delayed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => MaintenanceActivityStatus::Delayed,
                'completed_date' => null,
                'scheduled_date' => now()->addDays(7),
            ];
        });
    }
}
