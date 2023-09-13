<?php

namespace Assist\Interaction\Filament\Resources\InteractionInstitutionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionInstitutionResource;

class EditInteractionInstitution extends EditRecord
{
    protected static string $resource = InteractionInstitutionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
