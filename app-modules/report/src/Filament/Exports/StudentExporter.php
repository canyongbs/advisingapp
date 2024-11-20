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

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Actions\Exports\Exporter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\ServiceManagement\Models\ServiceRequestStatus;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    /**
     * @param class-string<TextColumn | ExportColumn> $type
     */
    public static function getColumns(string $type = ExportColumn::class): array
    {
        return [
            $type::make('sisid'),
            $type::make('otherid'),
            $type::make('first')
                ->label('First Name'),
            $type::make('last')
                ->label('Last Name'),
            static::notDefault($type::make('full_name')
                ->label('Full Name')),
            static::notDefault($type::make('preferred')
                ->label('Preferred Name')),
            $type::make('email')
                ->label('Email Address'),
            static::notDefault($type::make('email_2')
                ->label('Email Address 2')),
            static::notDefault($type::make('mobile')),
            $type::make('phone'),
            static::notDefault($type::make('address')),
            static::notDefault($type::make('address2')
                ->label('Address 2')),
            static::notDefault($type::make('address3')
                ->label('Address 3')),
            static::notDefault($type::make('city')),
            static::notDefault($type::make('state')),
            static::notDefault($type::make('postal')),
            static::notDefault($type::make('birthdate')),
            static::notDefault($type::make('hsgrad')
                ->label('High School Graduation')),
            static::notDefault($type::make('dfw')
                ->label('DFW')),
            static::notDefault($type::make('holds')),
            static::notDefault($type::make('ethnicity')),
            static::notDefault($type::make('lastlsmlogin')
                ->label('Last LMS Login')),
            static::notDefault($type::make('f_e_term')
                ->label('First Enrollment Term')),
            static::notDefault($type::make('mr_e_term')
                ->label('Most Recent Enrollment Term')),
            static::notDefault($type::make('programs_count')
                ->label('Count of Programs')
                ->counts('programs')),
            static::notDefault($type::make('enrollments_count')
                ->label('Count of Enrollments')
                ->counts('enrollments')),
            static::notDefault($type::make('enrollments_avg_unt_taken')
                ->label('Average Attempted Enrollments')
                ->avg('enrollments', 'unt_taken')),
            static::notDefault($type::make('enrollments_avg_unt_earned')
                ->label('Average Earned Enrollments')
                ->avg('enrollments', 'unt_earned')),
            static::notDefault($type::make('engagement_files_count')
                ->label('Count of Files')
                ->counts('engagementFiles')),
            static::notDefault($type::make('alerts_count')
                ->label('Count of Alerts')
                ->counts('alerts')),
            ...array_map(
                fn(SystemAlertStatusClassification $status): TextColumn | ExportColumn => static::notDefault($type::make("alerts_{$status->value}_count")
                    ->label("Count of {$status->getLabel()} Alerts")
                    ->counts([
                        "alerts as alerts_{$status->value}_count" => fn(Builder $query) => $query->status($status),
                    ])),
                SystemAlertStatusClassification::cases(),
            ),
            static::notDefault($type::make('tasks_count')
                ->label('Count of Tasks')
                ->counts('tasks')),
            ...array_map(
                fn(TaskStatus $status): TextColumn | ExportColumn => static::notDefault($type::make("tasks_{$status->value}_count")
                    ->label("Count of {$status->getLabel()} Tasks")
                    ->counts([
                        "tasks as tasks_{$status->value}_count" => fn(Builder $query) => $query->where('status', $status),
                    ])),
                TaskStatus::cases(),
            ),
            static::notDefault($type::make('subscribed_users_count')
                ->label('Count of Subscribers')
                ->counts('subscribedUsers')),
            static::notDefault($type::make('interactions_count')
                ->label('Count of Interactions')
                ->counts('interactions')),
            ...InteractionType::all()->map(fn(InteractionType $interactionType): TextColumn | ExportColumn => static::notDefault($type::make("interactions_{$interactionType->getKey()}_count")
                ->label("Count of {$interactionType->name} Interactions")
                ->counts([
                    "interactions as interactions_{$interactionType->getKey()}_count" => fn(Builder $query) => $query->whereBelongsTo($interactionType, 'type'),
                ]))),
            ...InteractionStatus::all()->map(fn(InteractionStatus $status): TextColumn | ExportColumn => static::notDefault($type::make("interactions_{$status->getKey()}_count")
                ->label("Count of {$status->name} Interactions")
                ->counts([
                    "interactions as interactions_{$status->getKey()}_count" => fn(Builder $query) => $query->whereBelongsTo($status, 'status'),
                ]))),
            static::notDefault($type::make('care_team_count')
                ->label('Count of Care Team Members')
                ->counts('careTeam')),
            static::notDefault($type::make('care_team_count')
                ->label('Count of Care Team Members')
                ->counts('careTeam')),
            static::notDefault($type::make('service_requests_count')
                ->label('Count of Service Requests')
                ->counts('serviceRequests')),
            ...ServiceRequestStatus::all()->map(fn(ServiceRequestStatus $status): TextColumn | ExportColumn => static::notDefault($type::make("service_requests_{$status->getKey()}_count")
                ->label("Count of {$status->name} Service Requests")
                ->counts([
                    "serviceRequests as service_requests_{$status->getKey()}_count" => fn(Builder $query) => $query->whereBelongsTo($status, 'status'),
                ]))),
            static::notDefault($type::make('event_attendee_records_count')
                ->label('Count of Events')
                ->counts('eventAttendeeRecords')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student report export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    protected static function notDefault(ExportColumn | TextColumn $column): ExportColumn | TextColumn
    {
        if ($column instanceof ExportColumn) {
            $column->enabledByDefault(false);
        }

        return $column;
    }
}
