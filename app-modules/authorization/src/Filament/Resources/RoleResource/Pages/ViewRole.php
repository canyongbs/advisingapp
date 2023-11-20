<?php

namespace Assist\Authorization\Filament\Resources\RoleResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Assist\Authorization\Filament\Resources\RoleResource;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
