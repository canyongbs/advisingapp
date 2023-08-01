<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Filament\Resources\CaseUpdateResource;

class ViewCaseUpdate extends ViewRecord
{
    protected static string $resource = CaseUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
