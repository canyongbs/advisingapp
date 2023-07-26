<?php

namespace Assist\CaseModule\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource;

class ViewCaseItemPriority extends ViewRecord
{
    protected static string $resource = CaseItemPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
