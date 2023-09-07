<?php

namespace Assist\Interaction\Filament\Resources\InteractionDriverResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
