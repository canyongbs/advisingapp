<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;

class ManageStudentFiles extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagementFiles';

    protected static ?string $navigationLabel = 'Files and Documents';

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
