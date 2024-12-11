<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\MeetingCenter\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use App\Support\FeatureAccessResponse;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class CalendarEventPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        if (! Gate::check(
            collect($this->requiredFeatures())->map(fn (Feature $feature) => $feature->getGateName())
        )) {
            return FeatureAccessResponse::deny();
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'calendar_event.view-any',
            denyResponse: 'You do not have permission to view calendar events.'
        );
    }

    public function view(Authenticatable $authenticatable, CalendarEvent $calendarEvent): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["calendar_event.{$calendarEvent->getKey()}.view"],
            denyResponse: 'You do not have permission to view this engagement response.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'calendar_event.create',
            denyResponse: 'You do not have permission to create calendar events.'
        );
    }

    public function update(Authenticatable $authenticatable, CalendarEvent $calendarEvent): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["calendar_event.{$calendarEvent->getKey()}.update"],
            denyResponse: 'You do not have permission to update this engagement response.'
        );
    }

    public function delete(Authenticatable $authenticatable, CalendarEvent $calendarEvent): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["calendar_event.{$calendarEvent->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this engagement response.'
        );
    }

    public function restore(Authenticatable $authenticatable, CalendarEvent $calendarEvent): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["calendar_event.{$calendarEvent->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this engagement response.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, CalendarEvent $calendarEvent): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["calendar_event.{$calendarEvent->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this engagement response.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ScheduleAndAppointments];
    }
}
