<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Listeners;

use AdvisingApp\Application\Events\ApplicationSubmissionStateEntered;
use AdvisingApp\Application\Events\ApplicationSubmissionStateExited;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Workflow\Enums\WorkflowTriggerEvent;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Features\AdmissionsStageWorkflowTriggersFeature;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Facades\DB;
use Throwable;

class TriggerApplicationSubmissionStageWorkflows implements ShouldQueueAfterCommit
{
    public function handleEntered(ApplicationSubmissionStateEntered $event): void
    {
        if (! AdmissionsStageWorkflowTriggersFeature::active()) {
            return;
        }

        $this->dispatchMatchingWorkflows(
            submission: $event->submission,
            state: $event->state,
            triggerEvent: WorkflowTriggerEvent::Enter,
        );
    }

    public function handleExited(ApplicationSubmissionStateExited $event): void
    {
        if (! AdmissionsStageWorkflowTriggersFeature::active()) {
            return;
        }

        $this->dispatchMatchingWorkflows(
            submission: $event->submission,
            state: $event->state,
            triggerEvent: WorkflowTriggerEvent::Exit,
        );
    }

    private function dispatchMatchingWorkflows(
        ApplicationSubmission $submission,
        ApplicationSubmissionState $state,
        WorkflowTriggerEvent $triggerEvent,
    ): void {
        if (is_null($submission->author)) {
            return;
        }

        $application = $submission->submissible;

        if (! $application instanceof Application) {
            return;
        }

        $application->loadMissing('workflowTriggers.workflow.workflowSteps.currentDetails');

        $matchingTriggers = $application->workflowTriggers
            ->where('sub_related_type', $state->getMorphClass())
            ->where('sub_related_id', $state->getKey())
            ->where('event', $triggerEvent);

        if ($matchingTriggers->isEmpty()) {
            return;
        }

        try {
            DB::beginTransaction();

            $matchingTriggers->each(function (WorkflowTrigger $workflowTrigger) use ($submission) {
                if (! $workflowTrigger->workflow?->is_enabled) {
                    return;
                }

                $workflowRun = new WorkflowRun(['started_at' => now()]);
                $workflowRun->related()->associate($submission->author);
                $workflowRun->workflowTrigger()->associate($workflowTrigger);
                $workflowRun->saveOrFail();

                $previousRunStep = null;

                $workflowTrigger->workflow->workflowSteps->each(function (WorkflowStep $step, int $index) use ($workflowRun, &$previousRunStep) {
                    assert($step->currentDetails instanceof WorkflowDetails);

                    $executeAt = null;

                    if ($index === 0) {
                        $executeAt = $this->getStepScheduledAt($step);
                    }

                    $workflowRunStep = new WorkflowRunStep([
                        'execute_at' => $executeAt,
                        'delay_minutes' => $step->delay_minutes,
                        'previous_workflow_run_step_id' => $previousRunStep?->id,
                    ]);

                    $workflowRunStep->workflowRun()->associate($workflowRun);
                    $workflowRunStep->details()->associate($step->currentDetails);

                    $workflowRunStep->saveOrFail();

                    $previousRunStep = $workflowRunStep;
                });
            });

            DB::commit();
        } catch (Throwable $error) {
            DB::rollBack();

            throw $error;
        }
    }

    private function getStepScheduledAt(WorkflowStep $workflowStep): Carbon
    {
        return now()->toMutable()->addMinutes($workflowStep->delay_minutes);
    }
}
