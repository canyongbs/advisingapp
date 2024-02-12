<?php

namespace AdvisingApp\MeetingCenter\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class EventAttendeePolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'event_attendee.view-any',
            denyResponse: 'You do not have permissions to view event attendees.'
        );
    }

    public function view(Authenticatable $authenticatable, EventAttendee $eventAttendee): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event_attendee.*.view', "event_attendee.{$eventAttendee->id}.view"],
            denyResponse: 'You do not have permissions to view this event attendee.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'event_attendee.create',
            denyResponse: 'You do not have permissions to create event attendees.'
        );
    }

    public function update(Authenticatable $authenticatable, EventAttendee $eventAttendee): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event_attendee.*.update', "event_attendee.{$eventAttendee->id}.update"],
            denyResponse: 'You do not have permissions to update this event attendee.'
        );
    }

    public function delete(Authenticatable $authenticatable, EventAttendee $eventAttendee): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event_attendee.*.delete', "event_attendee.{$eventAttendee->id}.delete"],
            denyResponse: 'You do not have permissions to delete this event attendee.'
        );
    }

    public function restore(Authenticatable $authenticatable, EventAttendee $eventAttendee): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event_attendee.*.restore', "event_attendee.{$eventAttendee->id}.restore"],
            denyResponse: 'You do not have permissions to restore this event attendee.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, EventAttendee $eventAttendee): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event_attendee.*.force-delete', "event_attendee.{$eventAttendee->id}.force-delete"],
            denyResponse: 'You do not have permissions to permanently delete this event attendee.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::EventManagement];
    }
}
