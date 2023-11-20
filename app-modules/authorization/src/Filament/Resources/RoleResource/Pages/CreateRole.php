<?php

namespace Assist\Authorization\Filament\Resources\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Assist\Authorization\Filament\Resources\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
}
