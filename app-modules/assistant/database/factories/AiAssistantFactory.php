<?php

namespace AdvisingApp\Assistant\Database\Factories;

use AdvisingApp\Assistant\Enums\AiAssistantType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Assistant\Models\Model>
 */
class AiAssistantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assistant_id' => fake()->uuid(),
            'name' => fake()->word(),
            'type' => AiAssistantType::Custom,
            'description' => fake()->sentence(),
            'instructions' => fake()->paragraph(),
            'knowledge' => fake()->paragraph(),
        ];
    }
}
