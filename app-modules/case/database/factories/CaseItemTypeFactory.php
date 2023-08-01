<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Models\CaseItemType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseItemType>
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
