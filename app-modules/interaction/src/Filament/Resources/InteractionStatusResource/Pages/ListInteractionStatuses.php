<?php

namespace Assist\Interaction\Filament\Resources\InteractionStatusResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInteractionStatuses extends ListRecords
{
    protected static string $resource = InteractionStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
