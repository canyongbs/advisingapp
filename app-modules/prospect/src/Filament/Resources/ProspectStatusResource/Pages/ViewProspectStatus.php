<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProspectStatus extends ViewRecord
{
    protected static string $resource = ProspectStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
