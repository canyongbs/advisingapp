<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages;

use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\StudentsRelationManager;

class ManageStudents extends StudentsRelationManager
{
    protected static string $resource = BasicNeedsProgramResource::class;

    protected static string $relationship = 'students';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function getNavigationLabel(): string
    {
        return 'Students';
    }
}
