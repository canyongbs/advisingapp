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

namespace AdvisingApp\ServiceManagement\Providers;

use Filament\Panel;
use App\Concerns\ImplementsGraphQL;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\ServiceManagement\Models\Sla;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\ServiceManagement\ServiceManagementPlugin;
use AdvisingApp\ServiceManagement\Models\ChangeRequestType;
use AdvisingApp\ServiceManagement\Models\ServiceRequestForm;
use AdvisingApp\ServiceManagement\Models\ServiceRequestType;
use AdvisingApp\ServiceManagement\Models\ChangeRequestStatus;
use AdvisingApp\ServiceManagement\Models\ServiceRequestStatus;
use AdvisingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AdvisingApp\ServiceManagement\Models\ChangeRequestResponse;
use AdvisingApp\ServiceManagement\Models\ServiceRequestHistory;
use AdvisingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AdvisingApp\ServiceManagement\Models\ServiceRequestPriority;
use AdvisingApp\ServiceManagement\Models\ServiceRequestFormField;
use AdvisingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AdvisingApp\ServiceManagement\Observers\ChangeRequestObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestObserver;
use AdvisingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestUpdateObserver;
use AdvisingApp\ServiceManagement\Models\ServiceRequestFormAuthentication;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestHistoryObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestAssignmentObserver;
use AdvisingApp\ServiceManagement\Observers\ServiceRequestFormSubmissionObserver;
use AdvisingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use AdvisingApp\ServiceManagement\Services\ServiceRequestNumber\SqidPlusSixServiceRequestNumberGenerator;

class ServiceManagementServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new ServiceManagementPlugin()));

        $this->app->bind(ServiceRequestNumberGenerator::class, SqidPlusSixServiceRequestNumberGenerator::class);
    }

    public function boot(): void
    {
        Relation::morphMap([
            'change_request_response' => ChangeRequestResponse::class,
            'change_request_status' => ChangeRequestStatus::class,
            'change_request_type' => ChangeRequestType::class,
            'change_request' => ChangeRequest::class,
            'service_request_assignment' => ServiceRequestAssignment::class,
            'service_request_form_authentication' => ServiceRequestFormAuthentication::class,
            'service_request_form_field' => ServiceRequestFormField::class,
            'service_request_form_step' => ServiceRequestFormStep::class,
            'service_request_form_submission' => ServiceRequestFormSubmission::class,
            'service_request_form' => ServiceRequestForm::class,
            'service_request_history' => ServiceRequestHistory::class,
            'service_request_priority' => ServiceRequestPriority::class,
            'service_request_status' => ServiceRequestStatus::class,
            'service_request_type' => ServiceRequestType::class,
            'service_request_update' => ServiceRequestUpdate::class,
            'service_request' => ServiceRequest::class,
            'sla' => Sla::class,
        ]);

        $this->registerObservers();

        $this->discoverSchema(__DIR__ . '/../../graphql/service-management.graphql');
    }

    protected function registerObservers(): void
    {
        ChangeRequest::observe(ChangeRequestObserver::class);

        ServiceRequest::observe(ServiceRequestObserver::class);
        ServiceRequestAssignment::observe(ServiceRequestAssignmentObserver::class);
        ServiceRequestFormSubmission::observe(ServiceRequestFormSubmissionObserver::class);
        ServiceRequestHistory::observe(ServiceRequestHistoryObserver::class);
        ServiceRequestUpdate::observe(ServiceRequestUpdateObserver::class);
    }
}
