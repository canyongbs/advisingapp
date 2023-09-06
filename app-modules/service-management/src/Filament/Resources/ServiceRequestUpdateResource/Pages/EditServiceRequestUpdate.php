<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource;

class EditServiceRequestUpdate extends EditRecord
{
    protected static string $resource = ServiceRequestUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
