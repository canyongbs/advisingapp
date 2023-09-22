<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

class ListServiceRequestUpdates extends ListRecords
{
    protected static string $resource = ServiceRequestUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
