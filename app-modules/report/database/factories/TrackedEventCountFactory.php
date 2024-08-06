<?php

namespace AdvisingApp\Report\Database\Factories;

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEventCount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackedEventCount>
 */
class TrackedEventCountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(TrackedEventType::cases()),
            'count' => fake()->numberBetween(1, 100),
            'last_occurred_at' => fake()->dateTime(),
        ];
    }
}
