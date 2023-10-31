<?php

namespace Assist\Alert\Filament\Resources\AlertResource\Pages;

use Assist\Alert\Filament\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlert extends ViewRecord
{
    protected static string $resource = AlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
