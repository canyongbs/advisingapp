<?php

namespace AdvisingApp\Ai\Database\Factories;

use AdvisingApp\Ai\Models\Prompt;
use AdvisingApp\Ai\Models\PromptUse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends PromptUse
 */
class PromptUseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prompt_id' => Prompt::factory(),
            'user_id' => User::factory()
        ];
    }
}
