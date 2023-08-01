<?php

namespace Assist\Case\Database\Factories;

use Assist\Case\Models\CaseItem;
use Assist\Case\Models\CaseUpdate;
use Assist\Case\Enums\CaseUpdateDirection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseUpdate>
 */
class CaseUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'case_id' => CaseItem::factory(),
            'update' => $this->faker->sentence(),
            'internal' => $this->faker->boolean(),
            'direction' => $this->faker->randomElement(CaseUpdateDirection::cases())->value,
        ];
    }
}
