<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Models\ServiceRequestType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestType>
 */
class CaseItemTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
