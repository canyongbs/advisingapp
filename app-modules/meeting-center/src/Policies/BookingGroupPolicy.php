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

namespace AdvisingApp\MeetingCenter\Policies;

use AdvisingApp\MeetingCenter\Models\BookingGroup;
use App\Concerns\PerformsFeatureChecks;
use App\Enums\Feature;
use App\Features\BookingGroupFeature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class BookingGroupPolicy
{
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! BookingGroupFeature::active()) {
            return Response::deny('The Booking Groups feature is not enabled for your account.');
        }

        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.view-any'],
            denyResponse: 'You do not have permissions to view booking groups.'
        );
    }

    public function view(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.*.view'],
            denyResponse: 'You do not have permissions to view this booking group.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.create'],
            denyResponse: 'You do not have permissions to create booking groups.'
        );
    }

    public function update(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.*.update'],
            denyResponse: 'You do not have permissions to update this booking group.'
        );
    }

    public function delete(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.*.delete'],
            denyResponse: 'You do not have permissions to delete this booking group.'
        );
    }

    public function restore(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.*.restore'],
            denyResponse: 'You do not have permissions to restore this booking group.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, BookingGroup $bookingGroup): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['group_appointment.*.force-delete'],
            denyResponse: 'You do not have permissions to force delete this booking group.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ScheduleAndAppointments];
    }
}
