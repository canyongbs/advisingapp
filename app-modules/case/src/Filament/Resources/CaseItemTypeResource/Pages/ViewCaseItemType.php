<?php

namespace Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

class ViewCaseItemType extends ViewRecord
{
    protected static string $resource = CaseItemTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
