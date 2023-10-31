<?php

namespace Assist\Alert\Filament\Resources\AlertResource\Pages;

use Assist\Alert\Filament\Resources\AlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlert extends EditRecord
{
    protected static string $resource = AlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
