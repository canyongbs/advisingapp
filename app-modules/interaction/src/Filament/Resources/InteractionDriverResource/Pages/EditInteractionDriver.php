<?php

namespace Assist\Interaction\Filament\Resources\InteractionDriverResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionDriverResource;

class EditInteractionDriver extends EditRecord
{
    protected static string $resource = InteractionDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
