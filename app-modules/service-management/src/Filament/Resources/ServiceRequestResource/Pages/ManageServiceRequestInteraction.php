<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\InteractionsRelationManager;

class ManageServiceRequestInteraction extends ManageRelatedRecords
{
    protected static string $resource = ServiceRequestResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'interactions';

    protected static ?string $navigationLabel = 'Interactions';

    protected static ?string $breadcrumb = 'Interactions';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function canAccess(?Model $record = null): bool
    {
        return (bool) count(static::managers($record));
    }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(Model $record): array
    {
        return collect([
            InteractionsRelationManager::class,
        ])
            ->reject(fn ($relationManager) => ! $relationManager::canViewForRecord($record, static::class))
            ->toArray();
    }
}
