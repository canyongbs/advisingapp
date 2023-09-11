<?php

namespace Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;

class EditInteractionOutcome extends EditRecord
{
    protected static string $resource = InteractionOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
