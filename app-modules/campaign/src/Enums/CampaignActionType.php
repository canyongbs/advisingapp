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

namespace AdvisingApp\Campaign\Enums;

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Campaign\Filament\Blocks\CareTeamBlock;
use AdvisingApp\Campaign\Filament\Blocks\CaseBlock;
use AdvisingApp\Campaign\Filament\Blocks\EngagementBatchEmailBlock;
use AdvisingApp\Campaign\Filament\Blocks\EngagementBatchSmsBlock;
use AdvisingApp\Campaign\Filament\Blocks\EventBlock;
use AdvisingApp\Campaign\Filament\Blocks\InteractionBlock;
use AdvisingApp\Campaign\Filament\Blocks\ProactiveAlertBlock;
use AdvisingApp\Campaign\Filament\Blocks\SubscriptionBlock;
use AdvisingApp\Campaign\Filament\Blocks\TaskBlock;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Task\Models\Task;
use App\Settings\LicenseSettings;
use Filament\Support\Contracts\HasLabel;

enum CampaignActionType: string implements HasLabel
{
    case BulkEngagementEmail = 'bulk_engagement_email';

    case BulkEngagementSms = 'bulk_engagement_sms';

    case Case = 'case';

    case ProactiveAlert = 'proactive_alert';

    case Interaction = 'interaction';

    case CareTeam = 'care_team';

    case Task = 'task';

    case Subscription = 'subscription';

    case Event = 'event';

    public static function blocks(): array
    {
        $blocks = [
            EngagementBatchEmailBlock::make(),
            EngagementBatchSmsBlock::make(),
            ProactiveAlertBlock::make(),
            InteractionBlock::make(),
            CareTeamBlock::make(),
            TaskBlock::make(),
            SubscriptionBlock::make(),
        ];

        if (app(LicenseSettings::class)->data->addons->caseManagement) {
            $blocks[] = CaseBlock::make();
        }

        if (app(LicenseSettings::class)->data->addons->eventManagement) {
            $blocks[] = EventBlock::make();
        }

        return $blocks;
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            CampaignActionType::BulkEngagementEmail => 'Email',
            CampaignActionType::BulkEngagementSms => 'Text Message',
            CampaignActionType::Case => 'Case',
            CampaignActionType::ProactiveAlert => 'Proactive Alert',
            CampaignActionType::CareTeam => 'Care Team',
            default => $this->name,
        };
    }

    public function getModel(): string
    {
        return match ($this) {
            CampaignActionType::BulkEngagementEmail => EngagementBatch::class,
            CampaignActionType::BulkEngagementSms => EngagementBatch::class,
            CampaignActionType::Case => CaseModel::class,
            CampaignActionType::ProactiveAlert => Alert::class,
            CampaignActionType::Interaction => Interaction::class,
            CampaignActionType::CareTeam => CareTeam::class,
            CampaignActionType::Task => Task::class,
            CampaignActionType::Subscription => Subscription::class,
            CampaignActionType::Event => Event::class,
        };
    }

    public function getEditFields(): array
    {
        $block = match ($this) {
            CampaignActionType::BulkEngagementEmail => EngagementBatchEmailBlock::make(),
            CampaignActionType::BulkEngagementSms => EngagementBatchSmsBlock::make(),
            CampaignActionType::Case => CaseBlock::make(),
            CampaignActionType::ProactiveAlert => ProactiveAlertBlock::make(),
            CampaignActionType::Interaction => InteractionBlock::make(),
            CampaignActionType::CareTeam => CareTeamBlock::make(),
            CampaignActionType::Task => TaskBlock::make(),
            CampaignActionType::Subscription => SubscriptionBlock::make(),
            CampaignActionType::Event => EventBlock::make(),
        };

        return $block->editFields();
    }

    public function getStepSummaryView(): string
    {
        return match ($this) {
            CampaignActionType::BulkEngagementEmail => 'filament.forms.components.campaigns.actions.bulk-engagement',
            CampaignActionType::BulkEngagementSms => 'filament.forms.components.campaigns.actions.bulk-engagement',
            CampaignActionType::Case => 'filament.forms.components.campaigns.actions.case',
            CampaignActionType::ProactiveAlert => 'filament.forms.components.campaigns.actions.proactive-alert',
            CampaignActionType::Interaction => 'filament.forms.components.campaigns.actions.interaction',
            CampaignActionType::CareTeam => 'filament.forms.components.campaigns.actions.care-team',
            CampaignActionType::Task => 'filament.forms.components.campaigns.actions.task',
            CampaignActionType::Subscription => 'filament.forms.components.campaigns.actions.subscription',
            CampaignActionType::Event => 'filament.forms.components.campaigns.actions.event',
        };
    }

    public function executeAction(CampaignAction $action): bool|string
    {
        return match ($this) {
            CampaignActionType::BulkEngagementEmail => EngagementBatch::executeFromCampaignAction($action),
            CampaignActionType::BulkEngagementSms => EngagementBatch::executeFromCampaignAction($action),
            CampaignActionType::Case => CaseModel::executeFromCampaignAction($action),
            CampaignActionType::ProactiveAlert => Alert::executeFromCampaignAction($action),
            CampaignActionType::Interaction => Interaction::executeFromCampaignAction($action),
            CampaignActionType::CareTeam => CareTeam::executeFromCampaignAction($action),
            CampaignActionType::Task => Task::executeFromCampaignAction($action),
            CampaignActionType::Subscription => Subscription::executeFromCampaignAction($action),
            CampaignActionType::Event => Event::executeFromCampaignAction($action),
        };
    }
}
