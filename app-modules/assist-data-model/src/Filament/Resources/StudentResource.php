<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\CreateStudent;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFiles;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentTasks;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentEngagement;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInformation;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInteractions;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\StudentEngagementTimeline;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentSubscriptions;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 1;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewStudent::class,
            ManageStudentInformation::class,
            ManageStudentEngagement::class,
            ManageStudentFiles::class,
            ManageStudentAlerts::class,
            ManageStudentTasks::class,
            ManageStudentSubscriptions::class,
            ManageStudentInteractions::class,
            StudentEngagementTimeline::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'manage-alerts' => ManageStudentAlerts::route('/{record}/alerts'),
            'manage-engagement' => ManageStudentEngagement::route('/{record}/engagement'),
            'manage-files' => ManageStudentFiles::route('/{record}/files'),
            'manage-information' => ManageStudentInformation::route('/{record}/information'),
            'manage-interactions' => ManageStudentInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageStudentSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageStudentTasks::route('/{record}/tasks'),
            'view' => ViewStudent::route('/{record}'),
            'engagement-timeline' => StudentEngagementTimeline::route('/{record}/engagement-timeline'),
        ];
    }
}
