<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Database\Factories;

use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

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
