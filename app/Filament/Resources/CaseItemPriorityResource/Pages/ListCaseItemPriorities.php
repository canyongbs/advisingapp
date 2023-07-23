<?php

namespace App\Filament\Resources\CaseItemPriorityResource\Pages;

use App\Filament\Resources\CaseItemPriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
