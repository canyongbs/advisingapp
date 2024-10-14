<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Resources\Concerns\HasGlobalSearchResultScoring;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFiles;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentTasks;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentEvents;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentCareTeam;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentPrograms;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentEngagement;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInformation;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\StudentServiceManagement;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInteractions;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\StudentEngagementTimeline;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentSubscriptions;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFormSubmissions;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentApplicationSubmissions;

class StudentResource extends Resource
{
    use HasGlobalSearchResultScoring;

    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationGroup = 'Retention CRM';

    protected static ?string $recordTitleAttribute = 'full_name';

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         ViewStudent::class,
    //         ManageStudentInformation::class,
    //         ManageStudentEngagement::class,
    //         ManageStudentFiles::class,
    //         ManageStudentAlerts::class,
    //         ManageStudentTasks::class,
    //         ManageStudentSubscriptions::class,
    //         ManageStudentInteractions::class,
    //         StudentEngagementTimeline::class,
    //         ManageStudentCareTeam::class,
    //         ManageStudentFormSubmissions::class,
    //         ManageStudentApplicationSubmissions::class,
    //         StudentServiceManagement::class,
    //         ManageStudentEvents::class,
    //         ManageStudentPrograms::class,
    //     ]);
    // }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        static::scoreGlobalSearchResults($query, $search, [
            'full_name' => 100,
            'email' => 75,
            'email_2' => 75,
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
            // 'manage-alerts' => ManageStudentAlerts::route('/{record}/alerts'),
            // 'manage-engagement' => ManageStudentEngagement::route('/{record}/engagement'),
            // 'manage-files' => ManageStudentFiles::route('/{record}/files'),
            // 'manage-form-submissions' => ManageStudentFormSubmissions::route('/{record}/form-submissions'),
            // 'manage-application-submissions' => ManageStudentApplicationSubmissions::route('/{record}/application-submissions'),
            // 'manage-information' => ManageStudentInformation::route('/{record}/information'),
            // 'manage-interactions' => ManageStudentInteractions::route('/{record}/interactions'),
            // 'manage-subscriptions' => ManageStudentSubscriptions::route('/{record}/subscriptions'),
            // 'manage-tasks' => ManageStudentTasks::route('/{record}/tasks'),
            'view' => ViewStudent::route('/{record}'),
            // 'timeline' => StudentEngagementTimeline::route('/{record}/timeline'),
            // 'care-team' => ManageStudentCareTeam::route('/{record}/care-team'),
            // 'service-management' => StudentServiceManagement::route('/{record}/service-management'),
            // 'events' => ManageStudentEvents::route('/{record}/events'),
            // 'programs' => ManageStudentPrograms::route('/{record}/programs'),
        ];
    }
}
