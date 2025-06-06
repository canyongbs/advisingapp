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

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Notifications\RegistrationLinkToEventAttendeeNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

class EventCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            if (! app(LicenseSettings::class)->data->addons->eventManagement) {
                // TODO: Change this to a custom execption and test it.
                throw new Exception('The Event Management addon is not enabled.');
            }

            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            $email = $educatable->primaryEmailAddress?->address;

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
            $this->actionEducatable
                ->related()
                ->make()
                ->related()
                ->associate($attendee)
                ->save();

            $this->actionEducatable->save();

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            if ($e->getModel() === Event::class) {
                $this->batch()->cancel();

                return;
            }

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}
