<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;

/**
 * @extends Factory<EventAttendee>
 */
class EventAttendeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->randomElement(EventAttendeeStatus::class),
            'email' => fake()->unique()->randomElement([
                fake()->email(),
                Student::inRandomOrder()->value('email'),
                Prospect::inRandomOrder()->value('email'),
            ]),
            'event_id' => Event::inRandomOrder()->first() ?? Event::factory()->create(),
        ];
    }
}
