<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowRunStep>
 */
class WorkflowRunStepFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workflow_run_id' => WorkflowRun::factory(),
            'details_type' => WorkflowEngagementEmailDetails::class,
            'details_id' => WorkflowEngagementEmailDetails::factory(),
            'execute_at' => now()->addMinutes($this->faker->numberBetween(1, 60)),
            'dispatched_at' => null,
            'succeeded_at' => null,
            'last_failed_at' => null,
        ];
    }

    public function executed(): static
    {
        return $this->state(fn (array $attributes) => [
            'dispatched_at' => now()->subMinutes(5),
            'succeeded_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'dispatched_at' => now()->subMinutes(5),
            'last_failed_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'execute_at' => now()->addMinutes(10),
            'dispatched_at' => null,
            'succeeded_at' => null,
            'last_failed_at' => null,
        ]);
    }
}
