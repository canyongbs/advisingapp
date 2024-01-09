<?php

namespace AdvisingApp\MeetingCenter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;

/**
 * @extends Factory<EventRegistrationFormStep>
 */
class EventRegistrationFormStepFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => str(fake()->word())->ucfirst(),
        ];
    }

    public function configure(): EventRegistrationFormStepFactory|Factory
    {
        return $this->afterCreating(function (EventRegistrationFormStep $eventRegistrationFormStep) {
            $fields = $this->createFields($eventRegistrationFormStep, rand(1, 3));

            $eventRegistrationFormStep->content = [
                'type' => 'doc',
                'content' => $fields,
            ];

            $eventRegistrationFormStep->save();
        });
    }

    private function createFields(EventRegistrationFormStep $eventRegistrationFormStep, int $count = 3): array
    {
        return EventRegistrationFormField::factory()
            ->count($count)
            ->create([
                'form_id' => $eventRegistrationFormStep->submissible->getKey(),
                'step_id' => $eventRegistrationFormStep->getKey(),
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
