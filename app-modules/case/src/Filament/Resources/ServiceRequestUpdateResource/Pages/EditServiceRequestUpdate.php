<?php

namespace Assist\Case\Filament\Resources\ServiceRequestUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Case\Filament\Resources\ServiceRequestUpdateResource;

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
