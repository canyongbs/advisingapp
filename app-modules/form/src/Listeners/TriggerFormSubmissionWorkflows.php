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

namespace AdvisingApp\Form\Listeners;

use AdvisingApp\Form\Events\FormSubmissionCreated;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Support\Facades\DB;
use Throwable;

class TriggerFormSubmissionWorkflows implements ShouldQueueAfterCommit
{
    public function handle(FormSubmissionCreated $event): void
    {
        $form = $event->submission->submissible;

        assert($form instanceof Form);

        if (is_null($event->submission->author)) {
            // If the submission has no author, we cannot trigger workflows.
            return;
        }

        try {
            DB::beginTransaction();

            $form->loadMissing('workflowTriggers.workflow.workflowSteps.currentDetails');

            $form->workflowTriggers->each(function (WorkflowTrigger $workflowTrigger) use ($event) {
                if (! $workflowTrigger->workflow->is_enabled) {
                    return;
                }

                $workflowRun = new WorkflowRun(['started_at' => now()]);
                $workflowRun->related()->associate($event->submission->author);
                $workflowRun->workflowTrigger()->associate($workflowTrigger);
                $workflowRun->saveOrFail();

                $previousRunStep = null;

                $workflowTrigger->workflow->workflowSteps->each(function (WorkflowStep $step, int $index) use ($event, $workflowRun, &$previousRunStep) {
                    assert($step->currentDetails instanceof WorkflowDetails);

                    $workflowRunStep = new WorkflowRunStep([
                        'execute_at' => $this->getStepScheduledAt($step, $event, $index),
                        'offset_minutes' => $step->delay_minutes,
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

    private function getStepScheduledAt(WorkflowStep $step, FormSubmissionCreated $event, int $index): ?Carbon
    {
        if ($index === 0) {
            $delayFrom = $event->submission->submitted_at->toMutable();
            $delayFrom->addMinutes($step->delay_minutes);

            return $delayFrom;
        }

        return null;
    }
}
