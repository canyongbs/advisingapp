<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Assist\Case\Filament\Resources\CaseUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
