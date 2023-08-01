<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Case\Filament\Resources\CaseUpdateResource;

class EditCaseUpdate extends EditRecord
{
    protected static string $resource = CaseUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
