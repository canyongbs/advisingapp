<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChangeRequestStatus extends EditRecord
{
    protected static string $resource = ChangeRequestStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
