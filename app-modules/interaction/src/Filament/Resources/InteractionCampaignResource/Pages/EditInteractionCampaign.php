<?php

namespace Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages;

use Assist\Interaction\Filament\Resources\InteractionCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInteractionCampaign extends EditRecord
{
    protected static string $resource = InteractionCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
