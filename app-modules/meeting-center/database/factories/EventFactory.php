<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AdvisingApp\MeetingCenter\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->catchPhrase(),
            'description' => fake()->optional()->paragraphs(asText: true),
            'location' => fake()->address(),
            'capacity' => fake()->numberBetween(1, 5000),
            'starts_at' => fake()->dateTimeBetween('-1 week', '+1 week'),
            'ends_at' => fn (array $attributes) => Carbon::parse($attributes['starts_at'])->add('1 hour'),
        ];
    }
}
