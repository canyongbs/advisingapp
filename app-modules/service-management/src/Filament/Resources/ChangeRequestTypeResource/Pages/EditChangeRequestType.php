<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChangeRequestType extends EditRecord
{
    protected static string $resource = ChangeRequestTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
