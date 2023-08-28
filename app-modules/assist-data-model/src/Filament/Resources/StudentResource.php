<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\SubscriptionsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class,
            EngagementsRelationManager::class,
            EngagementFilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
