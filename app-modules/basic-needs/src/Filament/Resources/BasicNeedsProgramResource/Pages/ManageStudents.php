<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages;

use Laravel\Pennant\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\StudentsRelationManager;

class ManageStudents extends ManageRelatedRecords
{
    protected static string $resource = BasicNeedsProgramResource::class;

    protected static string $relationship = 'students';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return 'Students';
    }

    public function getRelationManagers(): array
    {
        return [StudentsRelationManager::class];
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (Feature::active('manage-student-program')) {
            return true;
        }

        return false;
    }
}
