<?php

namespace Assist\Interaction\Filament\Resources\InteractionRelationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Interaction\Filament\Resources\InteractionRelationResource;

class EditInteractionRelation extends EditRecord
{
    protected static string $resource = InteractionRelationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
