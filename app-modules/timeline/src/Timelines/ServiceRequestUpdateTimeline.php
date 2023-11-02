<?php

namespace Assist\Timeline\Timelines;

use Filament\Actions\ViewAction;
use Assist\Timeline\Models\CustomTimeline;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Components\ServiceRequestUpdateViewAction;

// TODO Decide where these belong - might want to keep these in the context of the original module
class ServiceRequestUpdateTimeline extends CustomTimeline
{
    public function __construct(
        public ServiceRequestUpdate $serviceRequestUpdate
    ) {}

    public function icon(): string
    {
        return 'heroicon-o-adjustments-vertical';
    }

    public function sortableBy(): string
    {
        return $this->serviceRequestUpdate->created_at;
    }

    public function providesCustomView(): bool
    {
        return true;
    }

    public function renderCustomView(): string
    {
        return 'service-management::service-request-update-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return ServiceRequestUpdateViewAction::make()->record($this->serviceRequestUpdate);
    }
}
