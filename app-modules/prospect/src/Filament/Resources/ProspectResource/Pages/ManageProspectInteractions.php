<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\ManageInteractions;

class ManageProspectInteractions extends ManageInteractions
{
    protected static string $resource = ProspectResource::class;

    protected static string $interactableType = Prospect::class;
}
