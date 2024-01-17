<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChangeRequest extends EditRecord
{
    protected static string $resource = ChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
