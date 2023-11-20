<?php

namespace App\Filament\Resources\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager as FilamentRelationManager;

class RelationManager extends FilamentRelationManager
{
    public function isReadOnly(): bool
    {
        return false;
    }
}
