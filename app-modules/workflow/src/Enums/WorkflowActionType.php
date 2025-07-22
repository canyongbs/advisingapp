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

use AdvisingApp\Workflow\Filament\Blocks\CareTeamBlock;
use AdvisingApp\Workflow\Filament\Blocks\CaseBlock;
use AdvisingApp\Workflow\Filament\Blocks\EngagementEmailBlock;
use AdvisingApp\Workflow\Filament\Blocks\EngagementSmsBlock;
use AdvisingApp\Workflow\Filament\Blocks\EventBlock;
use AdvisingApp\Workflow\Filament\Blocks\InteractionBlock;
use AdvisingApp\Workflow\Filament\Blocks\ProactiveAlertBlock;
use AdvisingApp\Workflow\Filament\Blocks\SubscriptionBlock;
use AdvisingApp\Workflow\Filament\Blocks\TagsBlock;
use AdvisingApp\Workflow\Filament\Blocks\TaskBlock;
use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
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
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Field;
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

    /**
     * @return array<int, WorkflowActionBlock>
     */
    public static function blocks(): array
    {
        $blocks = [
            CareTeamBlock::make(),
            EngagementEmailBlock::make(),
            EngagementSmsBlock::make(),
            InteractionBlock::make(),
            ProactiveAlertBlock::make(),
            SubscriptionBlock::make(),
            TagsBlock::make(),
            TaskBlock::make(),
        ];

        if (app(LicenseSettings::class)->data->addons->caseManagement) {
            $blocks[] = CaseBlock::make();
        }

        if (app(LicenseSettings::class)->data->addons->eventManagement) {
            $blocks[] = EventBlock::make();
        }

        return $blocks;
    }

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

    public function getBlock(): WorkflowActionBlock
    {
        return match ($this) {
            WorkflowActionType::CareTeam => CareTeamBlock::make(),
            WorkflowActionType::Case => CaseBlock::make(),
            WorkflowActionType::EngagementEmail => EngagementEmailBlock::make(),
            WorkflowActionType::EngagementSms => EngagementSmsBlock::make(),
            WorkflowActionType::Event => EventBlock::make(),
            WorkflowActionType::Interaction => InteractionBlock::make(),
            WorkflowActionType::ProactiveAlert => ProactiveAlertBlock::make(),
            WorkflowActionType::Subscription => SubscriptionBlock::make(),
            WorkflowActionType::Tags => TagsBlock::make(),
            WorkflowActionType::Task => TaskBlock::make(),
        };
    }

    /**
     * @return array<int, covariant Field>
     */
    public function getEditFields(): array
    {
        return $this->getBlock()->editFields();
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
