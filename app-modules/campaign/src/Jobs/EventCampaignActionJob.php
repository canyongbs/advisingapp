<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Notifications\RegistrationLinkToEventAttendeeNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class EventCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            if (! app(LicenseSettings::class)->data->addons->eventManagement) {
                throw new Exception('The Event Management addon is not enabled.');
            }

            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            /** @var Educatable $educatable */
            $email = $educatable->primaryEmailAddress()->first();

            throw_if(
                ! $email,
                new Exception('The educatable model must have a primary email address.')
            );

            $action = $this->actionEducatable->campaignAction;

            $event = Event::query()->findOrFail($action->data['event']);

            if ($event->attendees()->where('email', $email)->exists()) {
                // The Educatable is already an attendee, so we can skip the action.
                $this->actionEducatable->succeeded_at = now();
                $this->actionEducatable->save();

                DB::commit();

                return;
            }

            $user = $action->campaign->createdBy;

            throw_if(
                ! $user instanceof User,
                new Exception('The user must be an instance of User.')
            );

            $attendee = $event->attendees()->create([
                'email' => $email,
                'status' => EventAttendeeStatus::Invited,
            ]);

            $attendee->notify(new RegistrationLinkToEventAttendeeNotification($event, $user));

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($attendee);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
