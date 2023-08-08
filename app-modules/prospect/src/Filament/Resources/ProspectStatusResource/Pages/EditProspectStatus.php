<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;

class EditProspectStatus extends EditRecord
{
    protected static string $resource = ProspectStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
