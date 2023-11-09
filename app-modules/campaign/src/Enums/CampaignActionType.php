<?php

namespace Assist\Campaign\Enums;

use Filament\Support\Contracts\HasLabel;

enum CampaignActionType: string implements HasLabel
{
    case BulkEngagement = 'bulk_engagement';

    case ServiceRequest = 'service_request';

    case ProactiveAlert = 'proactive_alert';

    case Interaction = 'interaction';

    case CareTeam = 'care_team';

    case Task = 'task';

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
}
