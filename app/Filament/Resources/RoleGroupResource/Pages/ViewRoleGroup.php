<?php

namespace App\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\RoleGroupResource;

class ViewRoleGroup extends ViewRecord
{
    protected static string $resource = RoleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
