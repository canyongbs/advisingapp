<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Interaction\Enums\InteractableType;
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
        return [
            'description' => $this->faker->paragraph(),
            'division_id' => Division::factory(),
            'end_datetime' => now()->addMinutes(5),
            'interactable_type' => $this->faker->randomElement([
                (new Student())->getMorphClass(),
                (new Prospect())->getMorphClass(),
                (new CaseModel())->getMorphClass(),
            ]),
            'interactable_id' => function (array $attributes) {
                return match ($attributes['interactable_type']) {
                    (new Student())->getMorphClass() => Student::factory(),
                    (new Prospect())->getMorphClass() => Prospect::factory(),
                    (new CaseModel())->getMorphClass() => CaseModel::factory(),
                    default => null,
                };
            },
            'interaction_driver_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionDriver::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionDriver::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'interaction_initiative_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionInitiative::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionInitiative::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'interaction_outcome_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionOutcome::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionOutcome::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'interaction_relation_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionRelation::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionRelation::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'interaction_status_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionStatus::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionStatus::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'interaction_type_id' => function(array $attributes) {
              return match ($attributes['interactable_type']) {
                    (new Prospect())->getMorphClass() => InteractionType::factory(['interactable_type' => InteractableType::Prospect]),
                    (new Student())->getMorphClass(), (new CaseModel())->getMorphClass() => InteractionType::factory(['interactable_type' => InteractableType::Student]),
                    default => null,
                };
            },
            'start_datetime' => now(),
            'subject' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
