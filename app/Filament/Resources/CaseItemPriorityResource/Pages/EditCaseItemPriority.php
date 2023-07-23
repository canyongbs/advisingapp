<?php

namespace App\Filament\Resources\CaseItemPriorityResource\Pages;

use App\Filament\Resources\CaseItemPriorityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaseItemPriority extends EditRecord
{
    protected static string $resource = CaseItemPriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
