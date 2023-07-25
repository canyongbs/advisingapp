<?php

namespace App\Filament\Resources\RoleGroupResource\Pages;

use App\Filament\Resources\RoleGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
