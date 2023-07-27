<?php

namespace Assist\Authorization\Filament\Resources\RoleGroupResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Resources\Pages\EditRecord;
use Assist\Authorization\Filament\Resources\RoleGroupResource;

class EditRoleGroup extends EditRecord
{
    protected static string $resource = RoleGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
