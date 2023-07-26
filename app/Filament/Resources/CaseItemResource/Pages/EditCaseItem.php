<?php

namespace App\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CaseItemResource;

class EditCaseItem extends EditRecord
{
    protected static string $resource = CaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
