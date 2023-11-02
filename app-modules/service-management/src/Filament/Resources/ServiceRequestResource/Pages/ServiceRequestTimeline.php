<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Assist\Timeline\Filament\Pages\TimelinePage;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

class ServiceRequestTimeline extends TimelinePage
{
    protected static string $resource = ServiceRequestResource::class;

    protected static ?string $navigationLabel = 'Timeline';

    public string $emptyStateMessage = 'There are is no timeline available for this Service Request.';

    public string $noMoreRecordsMessage = "You have reached the end of this service request's timeline.";

    public array $modelsToTimeline = [
        ServiceRequestUpdate::class,
    ];
}
