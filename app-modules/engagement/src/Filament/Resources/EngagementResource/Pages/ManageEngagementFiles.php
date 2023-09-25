<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\Prospect\Filament\Resources\ProspectResource\RelationManagers\EngagementFilesRelationManager;

class ManageEngagementFiles extends ManageRelatedRecords
{
    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagementFiles';

    protected static ?string $navigationLabel = 'Files';

    protected static ?string $breadcrumb = 'Files';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function canAccess(?Model $record = null): bool
    {
        /** @var RelationManager $relationManager */
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
