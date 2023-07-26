<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Filament\Resources\CaseItemResource;

class ViewCaseItem extends ViewRecord
{
    protected static string $resource = CaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
