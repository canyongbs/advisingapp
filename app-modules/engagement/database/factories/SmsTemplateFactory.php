<?php

namespace Assist\Engagement\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Engagement\Models\SmsTemplate>
 */
class SmsTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'description' => fake()->sentence,
            'content' => fake()->paragraph,
        ];
    }
}
