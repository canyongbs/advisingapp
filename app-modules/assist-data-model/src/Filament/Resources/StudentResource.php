<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Forms\Form;
use StudentEngagementTimeline;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 1;

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
            Pages\ManageStudentInteractions::class,
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
            'engagement_timeline' => StudentEngagementTimeline::route('/{record}/engagement-timeline'),
        ];
    }
}
