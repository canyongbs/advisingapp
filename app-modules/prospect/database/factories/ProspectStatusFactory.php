<?php

namespace Assist\Prospect\Database\Factories;

use Assist\Prospect\Models\ProspectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\Prospect\Enums\ProspectStatusColorOptions;
use Assist\Prospect\Enums\SystemProspectClassification;

/**
 * @extends Factory<ProspectStatus>
 */
class ProspectStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classification' => $this->faker->randomElement(SystemProspectClassification::cases()),
            'name' => $this->faker->word,
            'color' => $this->faker->randomElement(ProspectStatusColorOptions::cases()),
        ];
    }
}
