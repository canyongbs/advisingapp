<?php

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkflowRunStep>
 */
class WorkflowRunStepFactory extends Factory
{
    public function definition(): array
    {
        $detailFactories = [
            WorkflowEngagementEmailDetails::factory(),
            // WorkflowCareTeamDetails::factory(),
            // WorkflowCaseDetails::factory(),
            // WorkflowTaskDetails::factory(),
            // Add more as factories are created
        ];

        return [
            'workflow_run_id' => WorkflowRun::factory(),
            'details_id' => $this->faker->randomElement($detailFactories),
            'details_type' => function (array $attributes) {
                $possibleModels = [
                    WorkflowEngagementEmailDetails::class,
                    // WorkflowCareTeamDetails::class,
                    // WorkflowCaseDetails::class,
                    // WorkflowTaskDetails::class,
                ];

                foreach ($possibleModels as $modelClass) {
                    if ($modelClass::find($attributes['details_id'])) {
                        return (new $modelClass())->getMorphClass();
                    }
                }

                throw new Exception("Could not determine model type for details_id: {$attributes['details_id']}");
            },
            'execute_at' => now()->addMinutes($this->faker->numberBetween(1, 60)),
            'dispatched_at' => null,
            'succeeded_at' => null,
            'last_failed_at' => null,
        ];
    }

    public function withDetails(object $detailsModel): static
    {
        return $this->state(function () use ($detailsModel) {
            return [
                'details_type' => $detailsModel->getMorphClass(),
                'details_id' => $detailsModel->getKey(),
            ];
        });
    }
}
