<?php

namespace Assist\Prospect\Database\Factories;

use Assist\Prospect\Models\ProspectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\Prospect\Enums\ProspectStatusColorOptions;

/**
 * @extends Factory<ProspectStatus>
 */
class ProspectStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(ProspectStatusColorOptions::cases())->value,
        ];
    }
}
