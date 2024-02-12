<?php

namespace AdvisingApp\MeetingCenter\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class EventPolicy implements PerformsChecksBeforeAuthorization
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
            abilities: 'event.view-any',
            denyResponse: 'You do not have permissions to view events.'
        );
    }

    public function view(Authenticatable $authenticatable, Event $event): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event.*.view', "event.{$event->id}.view"],
            denyResponse: 'You do not have permissions to view this event.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'event.create',
            denyResponse: 'You do not have permissions to create events.'
        );
    }

    public function update(Authenticatable $authenticatable, Event $event): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event.*.update', "event.{$event->id}.update"],
            denyResponse: 'You do not have permissions to update this event.'
        );
    }

    public function delete(Authenticatable $authenticatable, Event $event): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event.*.delete', "event.{$event->id}.delete"],
            denyResponse: 'You do not have permissions to delete this event.'
        );
    }

    public function restore(Authenticatable $authenticatable, Event $event): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event.*.restore', "event.{$event->id}.restore"],
            denyResponse: 'You do not have permissions to restore this event.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Event $event): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['event.*.force-delete', "event.{$event->id}.force-delete"],
            denyResponse: 'You do not have permissions to permanently delete this event.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::EventManagement];
    }
}
