<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\CreatedByRelationManager;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\AssignedToRelationManager;

class ManageServiceRequestUser extends ManageRelatedRecords
{
    protected static string $resource = ServiceRequestResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'assignedTo';

    protected static ?string $navigationLabel = 'Related Users';

    protected static ?string $breadcrumb = 'Related Users';

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
            AssignedToRelationManager::class,
            CreatedByRelationManager::class,
        ])
            ->reject(fn ($relationManager) => ! $relationManager::canViewForRecord($record, static::class))
            ->toArray();
    }
}
