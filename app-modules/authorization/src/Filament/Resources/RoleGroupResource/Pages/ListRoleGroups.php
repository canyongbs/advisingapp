<?php

namespace Assist\Authorization\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Assist\Authorization\Filament\Resources\RoleGroupResource;

class ListRoleGroups extends ListRecords
{
    protected static string $resource = RoleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
