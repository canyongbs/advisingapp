<?php

namespace Assist\MeetingCenter\Database\Factories;

use Illuminate\Support\Carbon;
use Assist\MeetingCenter\Models\Event;
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
        $starts = fake()->dateTimeBetween('+1 hour', '+1 day');

        return [
            'title' => fake()->words(asText: true),
            'description' => fake()->optional()->sentence(),
            'starts_at' => $starts,
            'ends_at' => Carbon::parse($starts)->add('1 hour'),
        ];
    }
}
