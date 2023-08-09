<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Prospect\Filament\Resources\ProspectResource;

class EditProspect extends EditRecord
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
