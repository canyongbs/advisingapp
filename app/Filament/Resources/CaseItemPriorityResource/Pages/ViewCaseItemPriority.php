<?php

namespace App\Filament\Resources\CaseItemPriorityResource\Pages;

use App\Filament\Resources\CaseItemPriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

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
