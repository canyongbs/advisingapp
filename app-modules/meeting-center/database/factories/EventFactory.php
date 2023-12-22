<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Illuminate\Support\Carbon;
use AdvisingApp\MeetingCenter\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
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
