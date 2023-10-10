<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementFilesRelationManager;

class ManageProspectFiles extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagementFiles';

    protected static ?string $navigationLabel = 'Files and Documents';

    protected static ?string $breadcrumb = 'Files';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

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
            EngagementFilesRelationManager::class,
        ])
            ->reject(fn ($relationManager) => ! $relationManager::canViewForRecord($record, static::class))
            ->toArray();
    }
}
