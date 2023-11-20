<?php

namespace Assist\Prospect\Database\Factories;

use Assist\Prospect\Models\ProspectSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProspectSource>
 */
class ProspectSourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }
}
