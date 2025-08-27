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
}
