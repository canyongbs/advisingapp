<?php

namespace Assist\Interaction\Filament\Resources\InteractionCampaignResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource;

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
