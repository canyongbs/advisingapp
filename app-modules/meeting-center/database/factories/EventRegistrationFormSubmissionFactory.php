<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Illuminate\Support\Str;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

/**
 * @extends Factory<EventRegistrationFormSubmission>
 */
class EventRegistrationFormSubmissionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendee_status' => fake()->randomElement(EventAttendeeStatus::class),
            'submitted_at' => fake()->dateTime(),
            'form_id' => EventRegistrationForm::inRandomOrder()->first() ?? EventRegistrationForm::factory()->create(),
            'event_attendee_id' => EventAttendee::inRandomOrder()->first() ?? EventAttendee::factory()->create(),
        ];
    }

    public function configure(): EventRegistrationFormSubmissionFactory|Factory
    {
        return $this->afterCreating(function (EventRegistrationFormSubmission $eventRegistrationFormSubmission) {
            $eventRegistrationFormSubmission
                ->submissible
                ->fields
                ->each(function (EventRegistrationFormField $eventRegistrationFormField) use ($eventRegistrationFormSubmission) {
                    $eventRegistrationFormSubmission
                        ->fields()
                        ->attach($eventRegistrationFormField->getKey(), [
                            'id' => Str::orderedUuid(),
                            'response' => fake()->optional($eventRegistrationFormField->is_required, '')->words(asText: true),
                        ]);
                });
        });
    }
}
