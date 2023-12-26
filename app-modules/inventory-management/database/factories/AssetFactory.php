<?php

namespace AdvisingApp\InventoryManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\InventoryManagement\Models\AssetType;
use AdvisingApp\InventoryManagement\Models\AssetStatus;
use AdvisingApp\InventoryManagement\Models\AssetLocation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'serial_number' => fake()->isbn13(),
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'asset_type_id' => AssetType::factory(),
            'asset_status_id' => AssetStatus::factory(),
            'asset_location_id' => AssetLocation::factory(),
            'purchase_date' => fake()->dateTime(),
        ];
    }
}
