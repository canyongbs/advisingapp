<?php

namespace Assist\Authorization\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Assist\Authorization\Filament\Resources\PermissionResource;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
