<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
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

    protected static ?string $navigationGroup = 'Record Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

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

    public static function getGloballySearchableAttributes(): array
    {
        return ['sisid', 'otherid', 'full_name', 'email', 'email_2', 'mobile', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Student ID' => $record->sisid,
            'Other ID' => $record->otherid,
            'Email Address' => collect([$record->email, $record->email_id])->filter()->implode(', '),
            'Mobile' => $record->mobile,
            'Phone' => collect([$record->mobile, $record->phone])->filter()->implode(', '),
        ], fn (mixed $value): bool => filled($value));
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
