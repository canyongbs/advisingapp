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

use AdvisingApp\Form\Events\FormSubmitted;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerFormSubmissionWorkflows implements ShouldQueue
{

    public function handle(FormSubmitted $event): void
    {
        $workflowTriggerId = WorkflowTrigger::whereRelatedType(Form::class)
            ->whereRelatedId($event->formSubmission->getKey())
            ->first()
            ->getKey();

        $steps = Workflow::whereWorkflowTriggerId($workflowTriggerId)
            ->whereNotNull('deleted_at')
            ->whereIsEnabled()
            ->first()
            ->workflowSteps();

        $steps->each(function (WorkflowStep $step) use ($event, $workflowTriggerId) {
            WorkflowRunStep::create([
                'scheduled_at' => $this->getStepScheduledAt($step, $event),
                'workflow_run_id' => WorkflowRun::whereWorkflowTriggerId($workflowTriggerId),
                'details_id' => $step->details_id,
                'details_type' => $step->details_type,
            ]);
        });
    }

    private function getStepScheduledAt(WorkflowStep $step, FormSubmitted $event): DateTime
    {
        //TODO: nullsafe property access here???
        $delayFrom = $step->previousWorkflowStep->scheduled_at ?? $event->formSubmission->submitted_at;

        assert($delayFrom instanceof DateTime);

        return $delayFrom->modify("+{$step->delay_minutes} minutes");
    }
}
