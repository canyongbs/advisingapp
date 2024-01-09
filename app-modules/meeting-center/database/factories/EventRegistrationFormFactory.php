<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Filament\Support\Colors\Color;
use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

/**
 * @extends Factory<EventRegistrationForm>
 */
class EventRegistrationFormFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'embed_enabled' => fake()->boolean(),
            'allowed_domains' => fn ($attributes) => $attributes['embed_enabled'] ? [
                parse_url(config('app.url'), PHP_URL_HOST),
                parse_url(fake()->url(), PHP_URL_HOST),
            ] : [],
            'primary_color' => fake()->randomElement(collect(Color::all())->keys()),
            'rounding' => fake()->randomElement(Rounding::class),
            'is_wizard' => fake()->boolean(),
            'event_id' => fn (array $attributes) => $attributes['event_id'] ??= Event::factory()->create()->getKey(),
        ];
    }

    public function configure(): EventRegistrationForm|Factory
    {
        return $this->afterCreating(function (EventRegistrationForm $eventRegistrationForm) {
            if ($eventRegistrationForm->is_wizard) {
                EventRegistrationFormStep::factory()->count(rand(2, 5))->create([
                    'form_id' => $eventRegistrationForm->getKey(),
                ]);
            } else {
                $fields = $this->createFields($eventRegistrationForm, rand(1, 3));

                $eventRegistrationForm->content = [
                    'type' => 'doc',
                    'content' => $fields,
                ];

                $eventRegistrationForm->save();
            }

            if (fake()->boolean()) {
                EventRegistrationFormSubmission::factory()
                    ->count(rand(1, 10))
                    ->create([
                        'form_id' => $eventRegistrationForm->getKey(),
                    ])
                    ->each(
                        fn (EventRegistrationFormSubmission $eventRegistrationFormSubmission) => $eventRegistrationFormSubmission
                            ->author()
                            ->associate(EventAttendee::factory()->create([
                                'status' => $eventRegistrationFormSubmission->attendee_status,
                                'event_id' => $eventRegistrationForm->event->getKey(),
                            ]))
                            ->save()
                    );
            }
        });
    }

    private function createFields(EventRegistrationForm $eventRegistrationForm, int $count = 3): array
    {
        return EventRegistrationFormField::factory()
            ->count($count)
            ->create([
                'form_id' => $eventRegistrationForm->getKey(),
            ])
            ->map(fn (EventRegistrationFormField $field) => [
                'type' => 'tiptapBlock',
                'attrs' => [
                    'type' => $field->type,
                    'data' => [
                        'label' => $field->label,
                        'isRequired' => $field->is_required,
                    ],
                    'id' => $field->getKey(),
                ],
            ])
            ->toArray();
    }
}
