<?php

namespace Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

class EditCaseItemType extends EditRecord
{
    protected static string $resource = CaseItemTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
