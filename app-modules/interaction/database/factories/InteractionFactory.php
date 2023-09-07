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
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Assist\Interaction\Models\Interaction>
 */
class InteractionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
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
            'interactable_id' => $interactable->id,
            'interactable_type' => $interactable->getMorphClass(),
            'type_id' => InteractionType::factory(),
            'campaign_id' => InteractionCampaign::factory(),
            'driver_id' => InteractionDriver::factory(),
            'status_id' => InteractionStatus::factory(),
            'outcome_id' => InteractionOutcome::factory(),
            'start_datetime' => now(),
            'end_datetime' => now()->addMinutes(5),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];
    }
}
