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

namespace AdvisingApp\Workflow\Enums;

use AdvisingApp\Workflow\Jobs\CareTeamWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\CaseWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\EngagementEmailWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\EngagementSmsWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\EventWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionOnEducatableJob;
use AdvisingApp\Workflow\Jobs\InteractionWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\ProactiveAlertWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\SubscriptionWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\TagsWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\TaskWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Filament\Support\Contracts\HasLabel;

enum WorkflowActionType: string implements HasLabel
{
    case EngagementEmail = 'engagement_email';

    case EngagementSms = 'engagement_sms';

    case Case = 'case';

    case ProactiveAlert = 'proactive_alert';

    case Interaction = 'interaction';

    case CareTeam = 'care_team';

    case Task = 'task';

    case Subscription = 'subscription';

    case Event = 'event';

    case Tags = 'tags';

    public function getLabel(): string
    {
        return match ($this) {
            WorkflowActionType::EngagementEmail => 'Email',
            WorkflowActionType::EngagementSms => 'Text Message',
            WorkflowActionType::ProactiveAlert => 'Proactive Alert',
            WorkflowActionType::CareTeam => 'Care Team',
            default => $this->name,
        };
    }

    public function getActionExecutableJob(WorkflowRunStep $step): ExecuteWorkflowActionOnEducatableJob
    {
        return match ($this) {
            WorkflowActionType::CareTeam => new CareTeamWorkflowActionJob($step),
            WorkflowActionType::Case => new CaseWorkflowActionJob($step),
            WorkflowActionType::EngagementEmail => new EngagementEmailWorkflowActionJob($step),
            WorkflowActionType::EngagementSms => new EngagementSmsWorkflowActionJob($step),
            WorkflowActionType::Event => new EventWorkflowActionJob($step),
            WorkflowActionType::Interaction => new InteractionWorkflowActionJob($step),
            WorkflowActionType::ProactiveAlert => new ProactiveAlertWorkflowActionJob($step),
            WorkflowActionType::Subscription => new SubscriptionWorkflowActionJob($step),
            WorkflowActionType::Tags => new TagsWorkflowActionJob($step),
            WorkflowActionType::Task => new TaskWorkflowActionJob($step),
        };
    }
}
