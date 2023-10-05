<?php

namespace Assist\Authorization\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Assist\Authorization\Filament\Resources\PermissionResource;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    public function getTabs(): array
    {
        return [
            'web' => Tab::make('Web')
                ->modifyQueryUsing(fn (Builder $query) => $query->web()),
            'api' => Tab::make('Api')
                ->modifyQueryUsing(fn (Builder $query) => $query->api()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
