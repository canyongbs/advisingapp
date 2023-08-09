<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ViewProspect extends ViewRecord
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
