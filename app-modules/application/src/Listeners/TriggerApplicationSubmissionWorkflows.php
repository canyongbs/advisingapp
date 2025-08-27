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

namespace AdvisingApp\Application\Listeners;

use AdvisingApp\Application\Events\ApplicationSubmissionCreated;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Facades\DB;
use Throwable;

class TriggerApplicationSubmissionWorkflows implements ShouldQueueAfterCommit
{
    public function handle(ApplicationSubmissionCreated $event): void
    {
        $application = $event->submission->submissible;

        assert($application instanceof Application);

        if (is_null($event->submission->author)) {
            // If the submission has no author, we cannot trigger workflows.
            return;
        }

        try {
            DB::beginTransaction();

            $application->loadMissing('workflowTriggers.workflow.workflowSteps.currentDetails');

            $application->workflowTriggers->each(function (WorkflowTrigger $workflowTrigger) use ($event) {
                if (! $workflowTrigger->workflow->is_enabled) {
                    return;
                }

                $workflowRun = new WorkflowRun(['started_at' => now()]);
                $workflowRun->related()->associate($event->submission->author);
                $workflowRun->workflowTrigger()->associate($workflowTrigger);
                $workflowRun->saveOrFail();

                $workflowTrigger->workflow->workflowSteps->each(function (WorkflowStep $step) use ($event, $workflowRun) {
                    assert($step->currentDetails instanceof WorkflowDetails);

                    $workflowRunStep = new WorkflowRunStep([
                        'execute_at' => $this->getStepScheduledAt($step, $event),
                    ]);

                    $workflowRunStep->workflowRun()->associate($workflowRun);
                    $workflowRunStep->details()->associate($step->currentDetails);

                    $workflowRunStep->saveOrFail();
                });
            });

            DB::commit();
        } catch (Throwable $error) {
            DB::rollBack();

            throw $error;
        }
    }

    private function getStepScheduledAt(WorkflowStep $workflowStep, ApplicationSubmissionCreated $event): Carbon
    {
        $delayFrom = $event->submission->created_at->toMutable();

        $delayFrom->addMinutes($workflowStep->delay_minutes);

        $prevStep = $workflowStep->previousWorkflowStep;

        while (! is_null($prevStep)) {
            $delayFrom->addMinutes($prevStep->delay_minutes);

            $prevStep = $prevStep->previousWorkflowStep;
        }

        return $delayFrom;
    }
}
