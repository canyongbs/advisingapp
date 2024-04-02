<?php

namespace AdvisingApp\Assistant\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\Assistant\Models\Model>
 */
class AssistantChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => fake()->paragraph(),
            'from' => fake()->randomElement([AIChatMessageFrom::User, AIChatMessageFrom::Assistant]),
        ];
    }
}
