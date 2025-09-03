<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Workflow\Database\Factories;

use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
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
            WorkflowEngagementSmsDetails::factory(),
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
                    WorkflowEngagementSmsDetails::class,
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
