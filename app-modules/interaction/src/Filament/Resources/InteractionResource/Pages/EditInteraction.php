<?php

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInteraction extends EditRecord
{
    protected static string $resource = InteractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
