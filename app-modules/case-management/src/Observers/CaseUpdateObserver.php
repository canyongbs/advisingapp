<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\CaseManagement\Actions\NotifyCaseUsers;
use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Models\CaseUpdate;
use AdvisingApp\CaseManagement\Notifications\CaseUpdated;
use AdvisingApp\CaseManagement\Notifications\Concerns\FetchCaseTemplate;
use AdvisingApp\CaseManagement\Notifications\EducatableCaseUpdatedNotification;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Notification\Notifications\Channels\MailChannel;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Events\TimelineableRecordDeleted;
use App\Models\User;

class CaseUpdateObserver
{
    use FetchCaseTemplate;

    public function created(CaseUpdate $caseUpdate): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $caseUpdate);
        }

        TimelineableRecordCreated::dispatch($caseUpdate->case, $caseUpdate);

        $customerEmailTemplate = $this->fetchTemplate(
            $caseUpdate->case->priority->type,
            CaseEmailTemplateType::Update,
            CaseTypeEmailTemplateRole::Customer
        );

        if (
            ! $caseUpdate->internal
            && $caseUpdate->case->priority?->type->is_customers_case_update_email_enabled
        ) {
            $caseUpdate->case->respondent->notify(
                new EducatableCaseUpdatedNotification($caseUpdate->case, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $caseUpdate->case->priority->type,
            CaseEmailTemplateType::Update,
            CaseTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $caseUpdate->case->priority->type,
            CaseEmailTemplateType::Update,
            CaseTypeEmailTemplateRole::Auditor
        );

        app(NotifyCaseUsers::class)->execute(
            $caseUpdate->case,
            new CaseUpdated($caseUpdate, $managerEmailTemplate, MailChannel::class),
            $caseUpdate->case->priority?->type->is_managers_case_update_email_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseUpdate->case,
            new CaseUpdated($caseUpdate, $auditorEmailTemplate, MailChannel::class),
            false,
            $caseUpdate->case->priority?->type->is_auditors_case_update_email_enabled ?? false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseUpdate->case,
            new CaseUpdated($caseUpdate, $managerEmailTemplate, DatabaseChannel::class),
            $caseUpdate->case->priority?->type->is_managers_case_update_notification_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseUpdate->case,
            new CaseUpdated($caseUpdate, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $caseUpdate->case->priority?->type->is_auditors_case_update_notification_enabled ?? false,
        );
    }

    public function deleted(CaseUpdate $caseUpdate): void
    {
        TimelineableRecordDeleted::dispatch($caseUpdate->case, $caseUpdate);
    }
}
