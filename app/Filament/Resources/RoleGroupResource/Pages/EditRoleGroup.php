<?php

namespace App\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RoleGroupResource;

class EditRoleGroup extends EditRecord
{
    protected static string $resource = RoleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
