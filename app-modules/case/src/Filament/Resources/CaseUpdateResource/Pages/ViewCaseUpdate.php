<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Assist\Case\Filament\Resources\CaseUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

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
