<?php

namespace Assist\Interaction\Filament\Resources\InteractionOutcomeResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionOutcomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInteractionOutcomes extends ListRecords
{
    protected static string $resource = InteractionOutcomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
