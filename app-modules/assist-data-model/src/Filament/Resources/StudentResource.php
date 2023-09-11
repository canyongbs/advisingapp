<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Alert\Filament\RelationManagers\AlertsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;
use Assist\Task\Filament\Resources\TaskResource\RelationManagers\TasksRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\SubscriptionsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementResponsesRelationManager;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Engagement', [
                EngagementsRelationManager::class,
                EngagementResponsesRelationManager::class,
                EngagementFilesRelationManager::class,
            ]),
            SubscriptionsRelationManager::class,
            TasksRelationManager::class,
            InteractionsRelationManager::class,
            AlertsRelationManager::class,
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
