<?php

namespace Assist\CaseModule\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\CaseModule\Filament\Resources\CaseItemResource;

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
