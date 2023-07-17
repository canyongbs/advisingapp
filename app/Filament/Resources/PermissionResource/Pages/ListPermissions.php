<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Resources\PermissionResource;

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
