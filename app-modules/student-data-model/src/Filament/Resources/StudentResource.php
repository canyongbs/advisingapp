<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Student\Filament\Resources\StudentResource\Pages\CreateStudent;
use AdvisingApp\Student\Filament\Resources\StudentResource\Pages\EditStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ListStudents;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentCareTeam;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentSubscriptions;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentTasks;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudentActivityFeed;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Features\ProspectStudentRefactor;
use App\Filament\Resources\Concerns\HasGlobalSearchResultScoring;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudentResource extends Resource
{
    use HasGlobalSearchResultScoring;

    protected static ?string $model = Student::class;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationGroup = 'Retention CRM';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        if (! ProspectStudentRefactor::active()) {
            return parent::getGlobalSearchEloquentQuery();
        }

        return parent::getGlobalSearchEloquentQuery()->with(['emailAddresses:id,address', 'phoneNumbers:id,number', 'primaryEmailAddress:id,address', 'primaryPhoneNumber:id,number']);
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        if (ProspectStudentRefactor::active()) {
            $query->leftJoinRelationship('primaryEmailAddress');
        }

        static::scoreGlobalSearchResults($query, $search, [
            'full_name' => 100,
            ...(
                ProspectStudentRefactor::active()
              ? ['student_email_addresses.address' => 75]
              : ['email' => 75, 'email_2' => 75]
            ),
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'sisid',
            'otherid',
            'full_name',
            ...(
                ProspectStudentRefactor::active()
                    ? ['emailAddresses.address', 'phoneNumbers.number']
                    : ['email', 'email_2', 'mobile', 'phone']
            ),
            'preferred',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Student ID' => $record->sisid,
            'Other ID' => $record->otherid,
            'Email Address' => ProspectStudentRefactor::active() ? $record->primaryEmailAddress?->address : collect([$record->email, $record->email_id])->filter()->implode(', '),
            'Phone' => ProspectStudentRefactor::active() ? $record->primaryPhoneNumber?->number : collect([$record->mobile, $record->phone])->filter()->implode(', '),
            'Preferred Name' => $record->preferred,
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
            'view' => ViewStudent::route('/{record}'),
            'activity-feed' => ViewStudentActivityFeed::route('/{record}/activity'),
            'alerts' => ManageStudentAlerts::route('/{record}/alerts'),
            'care-team' => ManageStudentCareTeam::route('/{record}/care-team'),
            'subscriptions' => ManageStudentSubscriptions::route('/{record}/subscriptions'),
            'tasks' => ManageStudentTasks::route('/{record}/tasks'),
        ];
    }
}
