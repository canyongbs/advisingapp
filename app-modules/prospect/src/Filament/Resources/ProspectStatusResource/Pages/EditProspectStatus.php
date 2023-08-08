<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Assist\Prospect\Filament\Resources\ProspectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
