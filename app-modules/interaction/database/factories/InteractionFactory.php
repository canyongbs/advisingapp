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

namespace AdvisingApp\Interaction\Database\Factories;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
class InteractionFactory extends Factory
{
    public function definition(): array
    {
        $interactable = fake()->randomElement([
            Student::class,
            Prospect::class,
            CaseModel::class,
        ]);

        $interactable = match ($interactable) {
            Student::class => Student::inRandomOrder()->first() ?? Student::factory()->create(),
            Prospect::class => Prospect::factory()->create(),
            CaseModel::class => CaseModel::factory()->create([
                'case_number' => fake()->randomNumber(8),
            ]),
        };

        return [
            'description' => fake()->paragraph(),
            'division_id' => Division::factory(),
            'end_datetime' => now()->addMinutes(5),
            'interactable_id' => $interactable->getKey(),
            'interactable_type' => $interactable->getMorphClass(),
            'interaction_driver_id' => InteractionDriver::factory(),
            'interaction_initiative_id' => InteractionInitiative::factory(),
            'interaction_outcome_id' => InteractionOutcome::factory(),
            'interaction_relation_id' => InteractionRelation::factory(),
            'interaction_status_id' => InteractionStatus::factory(),
            'interaction_type_id' => InteractionType::factory(),
            'start_datetime' => now(),
            'subject' => fake()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
