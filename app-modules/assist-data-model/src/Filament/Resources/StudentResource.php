<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Alert\Filament\RelationManagers\AlertsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;
use Assist\Task\Filament\Resources\TaskResource\RelationManagers\TasksRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\PerformanceRelationManager;
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewStudent::class,
            Pages\ManageStudentInformation::class,
            Pages\ManageStudentEngagement::class,
            Pages\ManageStudentFiles::class,
            Pages\ManageStudentAlerts::class,
            Pages\ManageStudentTasks::class,
            Pages\ManageStudentSubscriptions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'manage-alerts' => Pages\ManageStudentAlerts::route('/{record}/alerts'),
            'manage-engagement' => Pages\ManageStudentEngagement::route('/{record}/engagement'),
            'manage-files' => Pages\ManageStudentFiles::route('/{record}/files'),
            'manage-information' => Pages\ManageStudentInformation::route('/{record}/information'),
            'manage-interactions' => Pages\ManageStudentInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => Pages\ManageStudentSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => Pages\ManageStudentTasks::route('/{record}/tasks'),
            'view' => Pages\ViewStudent::route('/{record}'),
        ];
    }
}
