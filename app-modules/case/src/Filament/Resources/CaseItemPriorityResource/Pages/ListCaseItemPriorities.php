<?php

namespace Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\Case\Filament\Resources\CaseItemPriorityResource;

class ListCaseItemPriorities extends ListRecords
{
    protected static string $resource = CaseItemPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
