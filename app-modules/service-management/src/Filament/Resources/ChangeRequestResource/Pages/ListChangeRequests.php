<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChangeRequests extends ListRecords
{
    protected static string $resource = ChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
