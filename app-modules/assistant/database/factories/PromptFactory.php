<?php

namespace AdvisingApp\Assistant\Database\Factories;

use AdvisingApp\Assistant\Models\Prompt;
use AdvisingApp\Assistant\Models\PromptType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str(fake()->unique()->words(asText: true))->ucfirst()->toString(),
            'description' => fake()->optional()->sentences(asText: true),
            'prompt' => fake()->sentences(asText: true),
            'type_id' => PromptType::query()->inRandomOrder()->first() ?? PromptType::factory()->create(),
        ];
    }
}
