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
        $application = $event->submission->submissible;

        assert($application instanceof Application);

        $application->workflows->each(function (Workflow $workflow) use ($event) {
            $workflowRun = new WorkflowRun(['started_at' => now()]);

            $workflowRun->related()->associate($event->submission->author);
            $workflowRun->workflowTrigger()->associate($workflow->workflowTrigger);

            $workflowRun->save();
        });

        $steps = collect();

        $application->workflowTriggers->each(function (WorkflowTrigger $workflowTrigger) use ($steps) {
            $steps->merge($workflowTrigger->workflow->workflowSteps);
        });

        $steps->each(function (WorkflowStep $workflowStep) use ($event) {
            assert($workflowStep->currentDetails instanceof WorkflowDetails);

            $workflowRunStep = new WorkflowRunStep(['execute_at' => $this->getStepScheduledAt($workflowStep, $event)]);

            $workflowRun = $workflowStep->workflow->workflowTrigger->workflowRun;
            
            $workflowRunStep->workflowRun()->associate($workflowRun);
            $workflowRunStep->details()->associate($workflowStep->currentDetails);

            $workflowRunStep->save();
        });
    }

    private function getStepScheduledAt(WorkflowStep $workflowStep, ApplicationSubmissionCreated $event): Carbon
    {
        $delayFrom = $event->submission->created_at->toMutable();

        $delayFrom->addMinutes($workflowStep->delay_minutes);

        $prevStep = $workflowStep->previousWorkflowStep;

        while(! is_null($prevStep)) {
            $delayFrom->addMinutes($prevStep->delay_minutes);

            $prevStep = $prevStep->previousWorkflowStep;
        }

        return $delayFrom;
    }
}