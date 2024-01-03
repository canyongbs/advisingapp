<?php

namespace AdvisingApp\Analytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\Analytics\Models\AnalyticsResourceSource;

/**
 * @extends Factory<AnalyticsResourceSource>
 */
class AnalyticsResourceSourceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => str(fake()->unique()->word())->ucfirst()->toString(),
        ];
    }
}
