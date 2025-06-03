<?php

namespace AdvisingApp\Interaction\Tests\Tenant\Filament\Actions\RequestFactories;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Exception;
use Worksome\RequestFactories\RequestFactory;

class BulkCreateInteractionActionRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        $interactable = $this->faker->randomElement([
            Student::class,
            Prospect::class,
            CaseModel::class,
        ]);

        throw_unless(in_array($interactable, [
            Student::class,
            Prospect::class,
            CaseModel::class,
        ], true), new Exception('Invalid interactable type'));

        $interactable = match ($interactable) {
            Student::class => Student::inRandomOrder()->first() ?? Student::factory()->create(),
            Prospect::class => Prospect::factory()->create(),
            CaseModel::class => CaseModel::factory()->create([
                'case_number' => $this->faker->randomNumber(8),
            ]),
        };

        return [
            'description' => $this->faker->paragraph(),
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
            'subject' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
