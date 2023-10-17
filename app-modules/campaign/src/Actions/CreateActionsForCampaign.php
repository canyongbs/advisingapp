<?php

namespace Assist\Campaign\Actions;

use Assist\Campaign\Models\Campaign;
use Assist\Campaign\DataTransferObjects\CampaignActionCreationData;

class CreateActionsForCampaign
{
    public function from(Campaign $campaign, CampaignActionCreationData $data): void
    {
        foreach ($data->actions as $action) {
            $campaign->actions()->create([
                'type' => $action,
                'data' => $data->actionsData[$action],
            ]);
        }
    }
}
