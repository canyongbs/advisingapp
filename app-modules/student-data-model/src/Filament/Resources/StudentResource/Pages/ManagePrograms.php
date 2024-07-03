<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\BasicNeedsProgramResource\RelationManagers\BasicNeedsProgramsRelationManager;

class ManagePrograms extends BasicNeedsProgramsRelationManager
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'basicNeedsPrograms';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function getNavigationLabel(): string
    {
        return 'Programs';
    }
}
