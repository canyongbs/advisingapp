<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\MeetingCenter\Models\Model>
 */
class EventRegistrationFormFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => str(fake()->word())->ucfirst(),
            'type' => fake()->randomElement(['text_input', 'text_area']),
            'is_required' => fake()->boolean(),
            'config' => [],
        ];
    }
}
