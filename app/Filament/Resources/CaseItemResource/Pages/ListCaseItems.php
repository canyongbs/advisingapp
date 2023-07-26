<?php

namespace App\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CaseItemResource;

class ListCaseItems extends ListRecords
{
    protected static string $resource = CaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
