<?php

namespace Assist\Engagement\Database\Factories;

use Assist\Engagement\Models\EngagementFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EngagementFile>
 */
class EngagementFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(3),
        ];
    }
}
