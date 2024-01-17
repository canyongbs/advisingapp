<?php

namespace AdvisingApp\Assistant\Database\Factories;

use AdvisingApp\Assistant\Models\PromptType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptType>
 */
class PromptTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str(fake()->unique()->words(asText: true))->ucfirst(),
            'description' => fake()->optional()->sentences(asText: true),
        ];
    }
}
