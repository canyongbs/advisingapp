<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChangeRequestTypes extends ListRecords
{
    protected static string $resource = ChangeRequestTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
