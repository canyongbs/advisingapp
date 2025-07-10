<?php

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
