<?php

namespace Assist\Campaign\Actions;

use Assist\Campaign\Models\Campaign;
use Assist\Campaign\DataTransferObjects\CampaignActionsCreationData;

class CreateActionsForCampaign
{
    public function from(Campaign $campaign, CampaignActionsCreationData $data): void
    {
        foreach ($data->actions as $action) {
            $campaign->actions()->create([
                'type' => $action->type,
                'data' => $action->data,
            ]);
        }
    }
}
