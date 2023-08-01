<?php

namespace Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

class ListCaseItemTypes extends ListRecords
{
    protected static string $resource = CaseItemTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
