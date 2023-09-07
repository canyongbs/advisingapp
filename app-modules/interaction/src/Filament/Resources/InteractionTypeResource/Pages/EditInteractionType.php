<?php

namespace Assist\Interaction\Filament\Resources\InteractionTypeResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
