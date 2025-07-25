<?php

namespace AdvisingApp\Application\Listeners;

use AdvisingApp\Application\Events\ApplicationSubmissionCreated;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class TriggerApplicationSubmissionWorkflows implements ShouldQueue
{
    public function handle(ApplicationSubmissionCreated $event): void
    {
        $workflowTrigger = WorkflowTrigger::where('related_type', Application::class)
            ->where('related_id', $event->submission->getKey())
            ->first();
        
        $steps = Workflow::where('workflow_trigger_id', $workflowTrigger->getKey())
            ->whereNotNull('deleted_at')
            ->where('is_enabled', true)
            ->first()
            ->workflowSteps();

        $steps->each(function (WorkflowStep $step) use ($event, $workflowTrigger) {
            assert($step->details instanceof WorkflowDetails);

            if(is_null($step->previous_step_id)) {
                $workflowRun = new WorkflowRun(['started_at' => now()]);

                $workflowRun->related()->associate($event->submission->author);

                $workflowRun->workflowTrigger()->associate($workflowTrigger);

                $workflowRun->save();
            }

            $workflowRunStep = new WorkflowRunStep(['execute_at' => $this->getStepScheduledAt($step, $event)]);

            $workflowRun = WorkflowRun::where('workflow_trigger_id', $workflowTrigger->getKey())->get();
            
            $workflowRunStep->workflowRun()->associate($workflowRun);

            $workflowRunStep->details()->associate($step->details);

            $workflowRunStep->save();
        });
    }

    private function getStepScheduledAt(WorkflowStep $step, ApplicationSubmissionCreated $event): Carbon
    {
        $delayFrom = $event->submission->created_at->toMutable();

        $delayFrom->addMinutes($step->delay_minutes);

        $prevStep = $step->previousWorkflowStep;

        while (! is_null($prevStep)) {
            $delayFrom->addMinutes($prevStep->delay_minutes);

            $prevStep = $prevStep->previousWorkflowStep;
        }

        return $delayFrom;
    }
}