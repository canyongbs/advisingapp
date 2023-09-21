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

    protected static ?string $navigationLabel = 'Files';

    protected static ?string $breadcrumb = 'Files';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function canAccess(?Model $record = null): bool
    {
        foreach ([
            EngagementFilesRelationManager::class,
        ] as $relationManager) {
            if (! $relationManager::canViewForRecord($record, static::class)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getRelationManagers(): array
    {
        return [
            EngagementFilesRelationManager::class,
        ];
    }
}
