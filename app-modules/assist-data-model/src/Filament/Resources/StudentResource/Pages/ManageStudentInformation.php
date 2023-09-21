<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\PerformanceRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use function Filament\authorize;

class ManageStudentInformation extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'programs';

    protected static ?string $navigationLabel = 'Information';

    protected static ?string $breadcrumb = 'Information';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public static function canAccess(?Model $record = null): bool
    {
        foreach ([
             ProgramsRelationManager::class,
             EnrollmentsRelationManager::class,
             PerformanceRelationManager::class,
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
            ProgramsRelationManager::class,
            EnrollmentsRelationManager::class,
            PerformanceRelationManager::class,
        ];
    }
}
