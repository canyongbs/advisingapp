<?php

namespace App\Filament\Resources\CaseItemResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\CaseItemResource;

class ViewCaseItem extends ViewRecord
{
    protected static string $resource = CaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
