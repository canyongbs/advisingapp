<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\AssistDataModel\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFiles;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentTasks;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentCareTeam;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentEngagement;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInformation;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInteractions;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\StudentEngagementTimeline;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentSubscriptions;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFormSubmissions;

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
            ManageStudentCareTeam::class,
            ManageStudentFormSubmissions::class,
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
            'manage-alerts' => ManageStudentAlerts::route('/{record}/alerts'),
            'manage-engagement' => ManageStudentEngagement::route('/{record}/engagement'),
            'manage-files' => ManageStudentFiles::route('/{record}/files'),
            'manage-form-submissions' => ManageStudentFormSubmissions::route('/{record}/form-submissions'),
            'manage-information' => ManageStudentInformation::route('/{record}/information'),
            'manage-interactions' => ManageStudentInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageStudentSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageStudentTasks::route('/{record}/tasks'),
            'view' => ViewStudent::route('/{record}'),
            'timeline' => StudentEngagementTimeline::route('/{record}/timeline'),
            'care-team' => ManageStudentCareTeam::route('/{record}/care-team'),
        ];
    }
}
