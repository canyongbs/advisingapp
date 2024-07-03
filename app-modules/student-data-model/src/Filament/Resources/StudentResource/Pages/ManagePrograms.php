<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Laravel\Pennant\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\BasicNeedsProgramResource\RelationManagers\BasicNeedsProgramsRelationManager;

class ManagePrograms extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'basicNeedsPrograms';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function getNavigationLabel(): string
    {
        return 'Programs';
    }

    public function getRelationManagers(): array
    {
        return [BasicNeedsProgramsRelationManager::class];
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (Feature::active('manage-student-program')) {
            return true;
        }

        return false;
    }
}
