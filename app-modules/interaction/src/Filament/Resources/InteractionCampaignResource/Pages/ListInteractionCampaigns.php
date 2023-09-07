<?php

namespace Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInteractionCampaigns extends ListRecords
{
    protected static string $resource = InteractionCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
