<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Workflow\Models\WorkflowProactiveAlertDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowProactiveAlertDetails>
 */
class WorkflowProactiveAlertDetailsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(),
            'severity' => $this->faker->randomElement(AlertSeverity::cases()),
            'status_id' => AlertStatus::factory(),
            'suggested_intervention' => $this->faker->sentence(),
        ];
    }
}
