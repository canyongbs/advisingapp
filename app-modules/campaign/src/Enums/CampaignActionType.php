<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Campaign\Enums;

use Assist\Task\Models\Task;
use Assist\Alert\Models\Alert;
use Assist\CareTeam\Models\CareTeam;
use Filament\Support\Contracts\HasLabel;
use Assist\Campaign\Models\CampaignAction;
use Assist\Interaction\Models\Interaction;
use Assist\Engagement\Models\EngagementBatch;
use Assist\Notifications\Models\Subscription;
use Assist\Campaign\Filament\Blocks\TaskBlock;
use Assist\Campaign\Filament\Blocks\CareTeamBlock;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Campaign\Filament\Blocks\InteractionBlock;
use Assist\Campaign\Filament\Blocks\SubscriptionBlock;
use Assist\Campaign\Filament\Blocks\ProactiveAlertBlock;
use Assist\Campaign\Filament\Blocks\ServiceRequestBlock;
use Assist\Campaign\Filament\Blocks\EngagementBatchBlock;

enum CampaignActionType: string implements HasLabel
{
    case BulkEngagement = 'bulk_engagement';

    case ServiceRequest = 'service_request';

    case ProactiveAlert = 'proactive_alert';

    case Interaction = 'interaction';

    case CareTeam = 'care_team';

    case Task = 'task';

    case Subscription = 'subscription';

    public static function blocks(): array
    {
        return [
            EngagementBatchBlock::make(),
            ServiceRequestBlock::make(),
            ProactiveAlertBlock::make(),
            InteractionBlock::make(),
            CareTeamBlock::make(),
            TaskBlock::make(),
            SubscriptionBlock::make(),
        ];
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            CampaignActionType::BulkEngagement => 'Email or Text',
            CampaignActionType::ServiceRequest => 'Service Request',
            CampaignActionType::ProactiveAlert => 'Proactive Alert',
            CampaignActionType::CareTeam => 'Care Team',
            default => $this->name,
        };
    }

    public function getModel(): string
    {
        return match ($this) {
            CampaignActionType::BulkEngagement => EngagementBatch::class,
            CampaignActionType::ServiceRequest => ServiceRequest::class,
            CampaignActionType::ProactiveAlert => Alert::class,
            CampaignActionType::Interaction => Interaction::class,
            CampaignActionType::CareTeam => CareTeam::class,
            CampaignActionType::Task => Task::class,
            CampaignActionType::Subscription => Subscription::class,
        };
    }

    public function getEditFields(): array
    {
        $block = match ($this) {
            CampaignActionType::BulkEngagement => EngagementBatchBlock::make(),
            CampaignActionType::ServiceRequest => ServiceRequestBlock::make(),
            CampaignActionType::ProactiveAlert => ProactiveAlertBlock::make(),
            CampaignActionType::Interaction => InteractionBlock::make(),
            CampaignActionType::CareTeam => CareTeamBlock::make(),
            CampaignActionType::Task => TaskBlock::make(),
            CampaignActionType::Subscription => SubscriptionBlock::make(),
        };

        return $block->editFields();
    }

    public function getStepSummaryView(): string
    {
        return match ($this) {
            CampaignActionType::BulkEngagement => 'filament.forms.components.campaigns.actions.bulk-engagement',
            CampaignActionType::ServiceRequest => 'filament.forms.components.campaigns.actions.service-request',
            CampaignActionType::ProactiveAlert => 'filament.forms.components.campaigns.actions.proactive-alert',
            CampaignActionType::Interaction => 'filament.forms.components.campaigns.actions.interaction',
            CampaignActionType::CareTeam => 'filament.forms.components.campaigns.actions.care-team',
            CampaignActionType::Task => 'filament.forms.components.campaigns.actions.task',
            CampaignActionType::Subscription => 'filament.forms.components.campaigns.actions.subscription',
        };
    }

    public function executeAction(CampaignAction $action): bool|string
    {
        return match ($this) {
            CampaignActionType::BulkEngagement => EngagementBatch::executeFromCampaignAction($action),
            CampaignActionType::ServiceRequest => ServiceRequest::executeFromCampaignAction($action),
            CampaignActionType::ProactiveAlert => Alert::executeFromCampaignAction($action),
            CampaignActionType::Interaction => Interaction::executeFromCampaignAction($action),
            CampaignActionType::CareTeam => CareTeam::executeFromCampaignAction($action),
            CampaignActionType::Task => Task::executeFromCampaignAction($action),
            CampaignActionType::Subscription => Subscription::executeFromCampaignAction($action),
        };
    }
}
