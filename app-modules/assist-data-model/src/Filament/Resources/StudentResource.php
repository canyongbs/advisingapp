<?php

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;
use Illuminate\Database\Eloquent\Model;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Records';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'full_name';

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
