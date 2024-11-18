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

namespace AdvisingApp\CaseManagement\Observers;

use App\Models\User;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use AdvisingApp\CaseManagement\Actions\CreateCaseHistory;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\CaseManagement\Exceptions\CaseNumberUpdateAttemptException;
use AdvisingApp\CaseManagement\Notifications\SendEducatableServiceRequestClosedNotification;
use AdvisingApp\CaseManagement\Notifications\SendEducatableServiceRequestOpenedNotification;
use AdvisingApp\CaseManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

class ServiceRequestObserver
{
    public function creating(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->service_request_number ??= app(ServiceRequestNumberGenerator::class)->generate();
    }

    public function created(ServiceRequest $serviceRequest): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequest);
        }

        if ($serviceRequest->status->classification === SystemCaseClassification::Open) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestOpenedNotification($serviceRequest));
        }
    }

    public function updating(ServiceRequest $serviceRequest): void
    {
        throw_if($serviceRequest->isDirty('service_request_number'), new CaseNumberUpdateAttemptException());
    }

    public function saving(ServiceRequest $serviceRequest): void
    {
        if ($serviceRequest->wasChanged('status_id')) {
            $serviceRequest->status_updated_at = now();
        }
    }

    public function saved(ServiceRequest $serviceRequest): void
    {
        CreateCaseHistory::dispatch($serviceRequest, $serviceRequest->getChanges(), $serviceRequest->getOriginal());

        if (
            $serviceRequest->wasChanged('status_id')
            && $serviceRequest->status->classification === SystemCaseClassification::Closed
        ) {
            $serviceRequest->respondent->notify(new SendEducatableServiceRequestClosedNotification($serviceRequest));
        }
    }
}
