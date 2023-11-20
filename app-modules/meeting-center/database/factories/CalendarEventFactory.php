<?php

namespace Assist\MeetingCenter\Database\Factories;

use Illuminate\Support\Carbon;
use Assist\MeetingCenter\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(asText: true),
            'description' => fake()->optional()->sentence(),
            'starts_at' => fake()->dateTimeBetween('+1 hour', '+1 day'),
            'ends_at' => fn (array $attributes) => Carbon::parse($attributes['starts_at'])->add('1 hour'),
        ];
    }
}
