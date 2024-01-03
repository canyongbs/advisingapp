<?php

namespace AdvisingApp\Analytics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategoryClassification;

/**
 * @extends Factory<AnalyticsResourceCategory>
 */
class AnalyticsResourceCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => str(fake()->unique()->word())->ucfirst()->toString(),
            'description' => fake()->optional()->sentences(asText: true),
            'classification' => fake()->randomElement(AnalyticsResourceCategoryClassification::cases()),
        ];
    }
}
