<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\PerformanceRelationManager;

class ManageStudentInformation extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'programs';

    protected static ?string $navigationLabel = 'Information';

    protected static ?string $breadcrumb = 'Information';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

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
            ProgramsRelationManager::class,
            EnrollmentsRelationManager::class,
            PerformanceRelationManager::class,
        ])
            ->reject(fn ($relationManager) => ! $relationManager::canViewForRecord($record, static::class))
            ->toArray();
    }
}
