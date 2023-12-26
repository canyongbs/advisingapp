<?php

namespace AdvisingApp\InventoryManagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\InventoryManagement\Models\AssetType>
 */
class AssetTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
