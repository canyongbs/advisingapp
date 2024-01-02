<?php

namespace AdvisingApp\Analytics\Database\Factories;

use AdvisingApp\Analytics\Models\AnalyticsResourceSource;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => str(fake()->unique()->word())->ucfirst(),
        ];
    }
}
