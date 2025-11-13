<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiAssistantLink>
 */
class AiAssistantLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ai_assistant_id' => AiAssistant::factory(),
            'parsing_results' => $this->faker->paragraph,
            'url' => $this->faker->url,
        ];
    }
}
