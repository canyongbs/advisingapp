<?php

namespace Assist\Authorization\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Assist\Authorization\Filament\Resources\RoleGroupResource;

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
