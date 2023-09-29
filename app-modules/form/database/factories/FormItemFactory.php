<?php

namespace Assist\Form\Database\Factories;

use Assist\Form\Models\FormItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormItem>
 */
class FormItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['text_input', 'text_area', 'select']);

        $content = match ($type) {
            'select' => json_decode('{"options":{"us":"United States","ca":"Canada","uk":"United Kingdom"}}'),
            default => [],
        };

        return [
            'label' => fake()->words(asText: true),
            'key' => fake()->unique()->word(),
            'type' => $type,
            'content' => $content,
        ];
    }
}
