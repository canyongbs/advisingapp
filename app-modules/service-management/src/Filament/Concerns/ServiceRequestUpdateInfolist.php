<?php

namespace Assist\ServiceManagement\Filament\Concerns;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;

// TODO Re-use this trait across other places where infolist is rendered
trait ServiceRequestUpdateInfolist
{
    public function serviceRequestUpdateInfolist(): array
    {
        return [
            TextEntry::make('serviceRequest.service_request_number')
                ->label('Service Request')
                ->translateLabel()
                ->url(fn (ServiceRequestUpdate $serviceRequestUpdate): string => ServiceRequestResource::getUrl('view', ['record' => $serviceRequestUpdate->serviceRequest]))
                ->color('primary'),
            IconEntry::make('internal')
                ->boolean(),
            TextEntry::make('direction')
                ->icon(fn (ServiceRequestUpdateDirection $state): string => $state->getIcon())
                ->formatStateUsing(fn (ServiceRequestUpdateDirection $state): string => $state->getLabel()),
            TextEntry::make('update')
                ->columnSpanFull(),
        ];
    }
}
