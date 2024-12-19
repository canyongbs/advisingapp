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

namespace AdvisingApp\Survey\Database\Factories;

use AdvisingApp\Survey\Models\Survey;
use AdvisingApp\Survey\Models\SurveyField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Survey>
 */
class SurveyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentences(asText: true),
            'embed_enabled' => fake()->boolean(),
            'allowed_domains' => [fake()->domainName()],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Survey $survey) {
            if ($survey->fields()->doesntExist()) {
                $survey->fields()->createMany(SurveyField::factory()->count(3)->make()->toArray());

                $survey->content = [
                    'type' => 'doc',
                    'content' => $survey->fields->map(fn (SurveyField $field): array => [
                        'type' => 'tiptapBlock',
                        'attrs' => [
                            'id' => $field->id,
                            'type' => $field->type,
                            'data' => [
                                'label' => $field->label,
                                'isRequired' => $field->is_required,
                                ...$field->config,
                            ],
                        ],
                    ])->all(),
                ];
                $survey->save();
            }
        });
    }
}
