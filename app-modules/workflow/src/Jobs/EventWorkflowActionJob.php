<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Notifications\RegistrationLinkToEventAttendeeNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowEventDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class EventWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    
    public function handle(): void
    {
        try {
            if(! app(LicenseSettings::class)->data->addons->eventManagement) {
                // TODO: Change this to a custom execption and test it.
                throw new Exception('The Event Management addon is not enabled.');
            }

            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $email = $educatable->primaryEmailAddress?->address;

            throw_if(
                ! $email,
                new Exception('The educatable model must have a primary email address.')
            );

            $details = WorkflowEventDetails::whereId($this->workflowRunStep->details_id)->first();

            $event = Event::query()->findOrFail($details->event_id);

            if($event->attendees()->where('email', $email)->exists()) {
                //The Educatable is already an attendee, so we can skip the action.
                WorkflowRunStepRelated::create([
                    'workflow_run_step_id' => $this->workflowRunStep->id,
                    'related' => $event,
                ]);

                DB::commit();

                return;
            }

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $attendee = $event->attendees()->create([
                'email' => $email,
                'status' => EventAttendeeStatus::Invited,
            ]);

            $attendee->notify(new RegistrationLinkToEventAttendeeNotification($event, $user));

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $event,
            ]);

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}
