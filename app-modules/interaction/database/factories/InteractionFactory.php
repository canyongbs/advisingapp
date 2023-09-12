<?php

namespace Assist\Interaction\Database\Factories;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\Interaction\Models\InteractionInstitution;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Interaction\Models\Interaction>
 */
class InteractionFactory extends Factory
{
    public function definition(): array
    {
        $interactable = fake()->randomElement([
            Student::class,
            Prospect::class,
            ServiceRequest::class,
        ]);

        $interactable = $interactable::factory()->create();

        return [
            'user_id' => User::factory(),
            'interactable_id' => $interactable->identifier(),
            'interactable_type' => $interactable->getMorphClass(),
            'interaction_type_id' => InteractionType::factory(),
            'interaction_relation_id' => InteractionRelation::factory(),
            'interaction_campaign_id' => InteractionCampaign::factory(),
            'interaction_driver_id' => InteractionDriver::factory(),
            'interaction_status_id' => InteractionStatus::factory(),
            'interaction_outcome_id' => InteractionOutcome::factory(),
            'interaction_institution_id' => InteractionInstitution::factory(),
            'start_datetime' => now(),
            'end_datetime' => now()->addMinutes(5),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];
    }
}
