<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\Case\Filament\Resources\CaseUpdateResource;

class ListCaseUpdates extends ListRecords
{
    protected static string $resource = CaseUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
