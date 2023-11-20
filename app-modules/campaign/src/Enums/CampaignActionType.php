<?php

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

    public function getLabel(): ?string
    {
        return match ($this) {
            CampaignActionType::BulkEngagement => 'Bulk Engagement',
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
        return match ($this) {
            CampaignActionType::BulkEngagement => EngagementBatchBlock::make()->editFields(),
            CampaignActionType::ServiceRequest => ServiceRequestBlock::make()->editFields(),
            CampaignActionType::ProactiveAlert => ProactiveAlertBlock::make()->editFields(),
            CampaignActionType::Interaction => InteractionBlock::make()->editFields(),
            CampaignActionType::CareTeam => CareTeamBlock::make()->editFields(),
            CampaignActionType::Task => TaskBlock::make()->editFields(),
            CampaignActionType::Subscription => SubscriptionBlock::make()->editFields(),
        };
    }

    public function getStepSummaryView(): string
    {
        return 'filament.forms.components.campaigns.actions.' . str($this->value)->slug();
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
