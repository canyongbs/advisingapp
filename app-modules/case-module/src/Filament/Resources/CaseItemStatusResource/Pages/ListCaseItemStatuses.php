<?php

namespace Assist\CaseModule\Filament\Resources\CaseItemStatusResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\CaseModule\Filament\Resources\CaseItemStatusResource;

class ListCaseItemStatuses extends ListRecords
{
    protected static string $resource = CaseItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
