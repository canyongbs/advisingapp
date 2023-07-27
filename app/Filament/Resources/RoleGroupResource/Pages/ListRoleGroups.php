<?php

namespace App\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RoleGroupResource;

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
