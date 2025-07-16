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

namespace AdvisingApp\CaseManagement\Observers;

use AdvisingApp\CaseManagement\Actions\CreateCaseHistory;
use AdvisingApp\CaseManagement\Actions\NotifyCaseUsers;
use AdvisingApp\CaseManagement\Cases\CaseNumber\Contracts\CaseNumberGenerator;
use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Exceptions\CaseNumberUpdateAttemptException;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Notifications\CaseClosed;
use AdvisingApp\CaseManagement\Notifications\CaseCreated;
use AdvisingApp\CaseManagement\Notifications\CaseStatusChanged;
use AdvisingApp\CaseManagement\Notifications\Concerns\FetchCaseTemplate;
use AdvisingApp\CaseManagement\Notifications\EducatableCaseClosedNotification;
use AdvisingApp\CaseManagement\Notifications\EducatableCaseOpenedNotification;
use AdvisingApp\CaseManagement\Notifications\EducatableCaseStatusChangeNotification;
use AdvisingApp\CaseManagement\Notifications\SendClosedCaseFeedbackNotification;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Notification\Notifications\Channels\MailChannel;
use App\Enums\Feature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class CaseObserver
{
    use FetchCaseTemplate;

    public function creating(CaseModel $case): void
    {
        $case->case_number ??= app(CaseNumberGenerator::class)->generate();
    }

    public function created(CaseModel $case): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $case);
        }

        $customerEmailTemplate = $this->fetchTemplate(
            $case->priority->type,
            CaseEmailTemplateType::Created,
            CaseTypeEmailTemplateRole::Customer
        );

        if (
            $case->status?->classification === SystemCaseClassification::Open
            && $case->priority?->type->is_customers_case_created_email_enabled
        ) {
            $case->respondent->notify(
                new EducatableCaseOpenedNotification($case, $customerEmailTemplate)
            );
        }

        $customerEmailTemplate = $this->fetchTemplate(
            $case->priority->type,
            CaseEmailTemplateType::Created,
            CaseTypeEmailTemplateRole::Customer
        );

        if (
            $case->status?->classification === SystemCaseClassification::Open
            && $case->priority?->type->is_customers_case_created_email_enabled
        ) {
            $case->respondent->notify(
                new EducatableCaseOpenedNotification($case, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $case->priority->type,
            CaseEmailTemplateType::Created,
            CaseTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $case->priority->type,
            CaseEmailTemplateType::Created,
            CaseTypeEmailTemplateRole::Auditor
        );

        app(NotifyCaseUsers::class)->execute(
            $case,
            new CaseCreated($case, $managerEmailTemplate, MailChannel::class),
            $case->priority?->type->is_managers_case_created_email_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $case,
            new CaseCreated($case, $auditorEmailTemplate, MailChannel::class),
            false,
            $case->priority?->type->is_auditors_case_created_email_enabled ?? false,
        );

        app(NotifyCaseUsers::class)->execute(
            $case,
            new CaseCreated($case, $managerEmailTemplate, DatabaseChannel::class),
            $case->priority?->type->is_managers_case_created_notification_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $case,
            new CaseCreated($case, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $case->priority?->type->is_auditors_case_created_notification_enabled ?? false,
        );
    }

    public function updating(CaseModel $case): void
    {
        throw_if($case->isDirty('case_number'), new CaseNumberUpdateAttemptException());
    }

    public function saving(CaseModel $case): void
    {
        if ($case->wasChanged('status_id')) {
            $case->status_updated_at = now();
        }
    }

    public function saved(CaseModel $case): void
    {
        CreateCaseHistory::dispatch($case, $case->getChanges(), $case->getOriginal());

        $customerEmailTemplate = $this->fetchTemplate(
            $case->priority->type,
            CaseEmailTemplateType::Closed,
            CaseTypeEmailTemplateRole::Customer
        );

        if (
            $case->wasChanged('status_id')
            && $case->status->classification === SystemCaseClassification::Closed
            && $case->priority?->type->is_customers_case_closed_email_enabled
        ) {
            $case->respondent->notify(new EducatableCaseClosedNotification($case, $customerEmailTemplate));
        }

        if (
            Gate::check(Feature::CaseManagement->getGateName()) &&
            $case?->priority?->type?->has_enabled_feedback_collection &&
            $case?->status?->classification == SystemCaseClassification::Closed &&
            ! $case?->feedback()->count()
        ) {
            if ($case->priority->type->is_customers_survey_response_email_enabled) {
                $customerEmailTemplateForSurveyResponse = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::SurveyResponse,
                    CaseTypeEmailTemplateRole::Customer
                );

                $case->respondent->notify(new SendClosedCaseFeedbackNotification($case, $customerEmailTemplateForSurveyResponse));
            } else {
                $case->respondent->notify(new SendClosedCaseFeedbackNotification($case, null));
            }
        }
    }

    public function updated(CaseModel $case): void
    {
        if ($case->wasChanged('status_id')) {
            if ($case->status?->classification === SystemCaseClassification::Closed) {
                $managerEmailTemplate = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::Closed,
                    CaseTypeEmailTemplateRole::Manager
                );

                $auditorEmailTemplate = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::Closed,
                    CaseTypeEmailTemplateRole::Auditor
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseClosed($case, $managerEmailTemplate, MailChannel::class),
                    $case->priority?->type->is_managers_case_closed_email_enabled ?? false,
                    false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseClosed($case, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $case->priority?->type->is_auditors_case_closed_email_enabled ?? false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseClosed($case, $managerEmailTemplate, DatabaseChannel::class),
                    $case->priority?->type->is_managers_case_closed_notification_enabled ?? false,
                    false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseClosed($case, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $case->priority?->type->is_auditors_case_closed_notification_enabled ?? false,
                );
            } elseif ($case->status) {
                $customerEmailTemplate = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::StatusChange,
                    CaseTypeEmailTemplateRole::Customer
                );

                if ($case->priority?->type->is_customers_case_status_change_email_enabled) {
                    $case->respondent->notify(new EducatableCaseStatusChangeNotification($case, $customerEmailTemplate));
                }

                $managerEmailTemplate = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::StatusChange,
                    CaseTypeEmailTemplateRole::Manager
                );

                $auditorEmailTemplate = $this->fetchTemplate(
                    $case->priority->type,
                    CaseEmailTemplateType::StatusChange,
                    CaseTypeEmailTemplateRole::Auditor
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseStatusChanged($case, $managerEmailTemplate, MailChannel::class),
                    $case->priority?->type->is_managers_case_status_change_email_enabled ?? false,
                    false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseStatusChanged($case, $auditorEmailTemplate, MailChannel::class),
                    false,
                    $case->priority?->type->is_auditors_case_status_change_email_enabled ?? false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseStatusChanged($case, $managerEmailTemplate, DatabaseChannel::class),
                    $case->priority?->type->is_managers_case_status_change_notification_enabled ?? false,
                    false,
                );

                app(NotifyCaseUsers::class)->execute(
                    $case,
                    new CaseStatusChanged($case, $auditorEmailTemplate, DatabaseChannel::class),
                    false,
                    $case->priority?->type->is_auditors_case_status_change_notification_enabled ?? false,
                );
            }
        }
    }
}
