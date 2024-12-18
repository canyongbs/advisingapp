<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use Illuminate\Database\Eloquent\Factories\Factory;

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
