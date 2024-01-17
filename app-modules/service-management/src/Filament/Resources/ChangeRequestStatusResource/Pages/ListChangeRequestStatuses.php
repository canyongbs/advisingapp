<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChangeRequestStatuses extends ListRecords
{
    protected static string $resource = ChangeRequestStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
