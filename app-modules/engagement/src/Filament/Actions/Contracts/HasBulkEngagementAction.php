<?php

namespace Assist\Engagement\Filament\Actions\Contracts;

use Filament\Actions\Action;

interface HasBulkEngagementAction
{
    public function cancelBulkEngagementAction(): Action;
}
