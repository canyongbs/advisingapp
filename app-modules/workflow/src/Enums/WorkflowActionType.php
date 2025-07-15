<?php

namespace AdvisingApp\Workflow\Enums;

use AdvisingApp\Workflow\Jobs\EngagementEmailWorkflowActionJob;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionOnEducatableJob;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Exception;
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
            WorkflowActionType::EngagementEmail => new EngagementEmailWorkflowActionJob($step),
            // WorkflowActionType::EngagementSms => new EngagementWorkflowActionJob($step),
            // WorkflowActionType::Event => new EventCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::Case => new CaseCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::ProactiveAlert => new ProactiveAlertCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::Interaction => new InteractionCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::CareTeam => new CareTeamCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::Task => new TaskCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::Subscription => new SubscriptionCampaignActionJob($campaignActionEducatable),
            // WorkflowActionType::Tags => new TagsCampaignActionJob($campaignActionEducatable),
            default => throw new Exception('Invalid workflow action.'),
        };
    }
}
