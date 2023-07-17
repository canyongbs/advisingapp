<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

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
        return [
            Actions\CreateAction::make(),
        ];
    }
}
