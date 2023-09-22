<?php

namespace Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionTypeResource;

class EditInteractionType extends EditRecord
{
    protected static string $resource = InteractionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
