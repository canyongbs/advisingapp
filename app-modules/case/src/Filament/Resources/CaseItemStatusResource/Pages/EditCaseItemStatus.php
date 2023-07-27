<?php

namespace Assist\Case\Filament\Resources\CaseItemStatusResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Case\Filament\Resources\CaseItemStatusResource;

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
