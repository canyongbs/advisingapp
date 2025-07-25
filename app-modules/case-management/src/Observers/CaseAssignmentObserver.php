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

use AdvisingApp\CaseManagement\Actions\NotifyCaseUsers;
use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Enums\CaseTypeEmailTemplateRole;
use AdvisingApp\CaseManagement\Models\CaseAssignment;
use AdvisingApp\CaseManagement\Notifications\CaseAssigned;
use AdvisingApp\CaseManagement\Notifications\Concerns\FetchCaseTemplate;
use AdvisingApp\CaseManagement\Notifications\EducatableCaseAssignedNotification;
use AdvisingApp\Notification\Events\TriggeredAutoSubscription;
use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Notification\Notifications\Channels\MailChannel;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;
use AdvisingApp\Timeline\Events\TimelineableRecordDeleted;
use App\Models\User;

class CaseAssignmentObserver
{
    use FetchCaseTemplate;

    public function created(CaseAssignment $caseAssignment): void
    {
        $user = auth()->user();

        if ($user instanceof User) {
            TriggeredAutoSubscription::dispatch($user, $caseAssignment);
        }

        $caseAssignment->case->assignments()->where('id', '!=', $caseAssignment->id)->update([
            'status' => CaseAssignmentStatus::Inactive,
        ]);

        TimelineableRecordCreated::dispatch($caseAssignment->case, $caseAssignment);

        $customerEmailTemplate = $this->fetchTemplate(
            $caseAssignment->case->priority->type,
            CaseEmailTemplateType::Assigned,
            CaseTypeEmailTemplateRole::Customer
        );

        if ($caseAssignment->case->priority?->type->is_customers_case_assigned_email_enabled) {
            $caseAssignment->case->respondent->notify(
                new EducatableCaseAssignedNotification($caseAssignment->case, $customerEmailTemplate)
            );
        }

        $managerEmailTemplate = $this->fetchTemplate(
            $caseAssignment->case->priority->type,
            CaseEmailTemplateType::Assigned,
            CaseTypeEmailTemplateRole::Manager
        );

        $auditorEmailTemplate = $this->fetchTemplate(
            $caseAssignment->case->priority->type,
            CaseEmailTemplateType::Assigned,
            CaseTypeEmailTemplateRole::Auditor
        );

        app(NotifyCaseUsers::class)->execute(
            $caseAssignment->case,
            new CaseAssigned($caseAssignment->case, $managerEmailTemplate, MailChannel::class),
            $caseAssignment->case->priority?->type->is_managers_case_assigned_email_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseAssignment->case,
            new CaseAssigned($caseAssignment->case, $managerEmailTemplate, DatabaseChannel::class),
            $caseAssignment->case->priority?->type->is_managers_case_assigned_notification_enabled ?? false,
            false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseAssignment->case,
            new CaseAssigned($caseAssignment->case, $auditorEmailTemplate, MailChannel::class),
            false,
            $caseAssignment->case->priority?->type->is_auditors_case_assigned_email_enabled ?? false,
        );

        app(NotifyCaseUsers::class)->execute(
            $caseAssignment->case,
            new CaseAssigned($caseAssignment->case, $auditorEmailTemplate, DatabaseChannel::class),
            false,
            $caseAssignment->case->priority?->type->is_auditors_case_assigned_notification_enabled ?? false,
        );
    }

    public function deleted(CaseAssignment $caseAssignment): void
    {
        TimelineableRecordDeleted::dispatch($caseAssignment->case, $caseAssignment);
    }
}
