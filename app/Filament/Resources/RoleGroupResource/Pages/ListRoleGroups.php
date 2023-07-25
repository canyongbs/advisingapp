<?php

namespace App\Filament\Resources\RoleGroupResource\Pages;

use App\Filament\Resources\RoleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoleGroups extends ListRecords
{
    protected static string $resource = RoleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
