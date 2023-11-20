<?php

namespace Assist\Campaign\Models\Contracts;

use Assist\Campaign\Models\CampaignAction;

interface ExecutableFromACampaignAction
{
    public static function executeFromCampaignAction(CampaignAction $action): bool|string;
}
