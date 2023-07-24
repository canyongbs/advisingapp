<?php

namespace App\Filament\Resources\CaseItemStatusResource\Pages;

use App\Filament\Resources\CaseItemStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseItemStatus extends EditRecord
{
    protected static string $resource = CaseItemStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
