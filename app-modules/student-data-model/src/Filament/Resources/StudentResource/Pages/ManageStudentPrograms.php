<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Laravel\Pennant\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\ProgramRelationManager;

class ManageStudentPrograms extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'basicNeedsPrograms';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $breadcrumb = 'Programs';

    protected static ?string $title = 'Programs';

    public static function getNavigationLabel(): string
    {
        return 'Programs';
    }

    public function getRelationManagers(): array
    {
        return [ProgramRelationManager::class];
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (Feature::active('manage-program-participants')) {
            return true;
        }

        return false;
    }
}
