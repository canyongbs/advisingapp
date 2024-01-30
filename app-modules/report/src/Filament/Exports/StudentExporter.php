<?php

namespace AdvisingApp\Report\Filament\Exports;

use AdvisingApp\Task\Enums\TaskStatus;
use Filament\Actions\Exports\Exporter;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Alert\Enums\AlertStatus;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Models\InteractionStatus;
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
            $type::make('full_name')
                ->label('Full Name'),
            $type::make('preferred')
                ->label('Preferred Name'),
            $type::make('email')
                ->label('Email Address'),
            $type::make('email_2')
                ->label('Email Address 2'),
            $type::make('mobile'),
            $type::make('phone'),
            $type::make('address'),
            $type::make('address2')
                ->label('Address 2'),
            $type::make('address3')
                ->label('Address 3'),
            $type::make('city'),
            $type::make('state'),
            $type::make('postal'),
            $type::make('birthdate'),
            $type::make('hsgrad')
                ->label('High School Graduation'),
            $type::make('dfw')
                ->label('DFW'),
            $type::make('holds'),
            $type::make('ethnicity'),
            $type::make('lastlsmlogin')
                ->label('Last LMS Login'),
            $type::make('f_e_term')
                ->label('First Enrollment Term'),
            $type::make('mr_e_term')
                ->label('Most Recent Enrollment Term'),
            $type::make('programs_count')
                ->label('Count of Programs')
                ->counts('programs'),
            $type::make('enrollments_count')
                ->label('Count of Enrollments')
                ->counts('enrollments'),
            $type::make('enrollments_avg_unt_taken')
                ->label('Average Attempted Enrollments')
                ->avg('enrollments', 'unt_taken'),
            $type::make('enrollments_avg_unt_earned')
                ->label('Average Earned Enrollments')
                ->avg('enrollments', 'unt_earned'),
            $type::make('engagement_files_count')
                ->label('Count of Files')
                ->counts('engagementFiles'),
            $type::make('alerts_count')
                ->label('Count of Alerts')
                ->counts('alerts'),
            ...array_map(
                fn (AlertStatus $status): TextColumn | ExportColumn => $type::make("alerts_{$status->value}_count")
                    ->label("Count of {$status->getLabel()} Alerts")
                    ->counts([
                        "alerts as alerts_{$status->value}_count" => fn (Builder $query) => $query->status($status),
                    ]),
                AlertStatus::cases(),
            ),
            $type::make('tasks_count')
                ->label('Count of Tasks')
                ->counts('tasks'),
            ...array_map(
                fn (TaskStatus $status): TextColumn | ExportColumn => $type::make("tasks_{$status->value}_count")
                    ->label("Count of {$status->getLabel()} Tasks")
                    ->counts([
                        "tasks as tasks_{$status->value}_count" => fn (Builder $query) => $query->where('status', $status),
                    ]),
                TaskStatus::cases(),
            ),
            $type::make('subscribed_users_count')
                ->label('Count of Subscribers')
                ->counts('subscribedUsers'),
            $type::make('interactions_count')
                ->label('Count of Interactions')
                ->counts('interactions'),
            ...InteractionType::all()->map(fn (InteractionType $interactionType): TextColumn | ExportColumn => $type::make("interactions_{$interactionType->getKey()}_count")
                ->label("Count of {$interactionType->name} Interactions")
                ->counts([
                    "interactions as interactions_{$interactionType->getKey()}_count" => fn (Builder $query) => $query->whereBelongsTo($interactionType, 'type'),
                ])),
            ...InteractionStatus::all()->map(fn (InteractionStatus $status): TextColumn | ExportColumn => $type::make("interactions_{$status->getKey()}_count")
                ->label("Count of {$status->name} Interactions")
                ->counts([
                    "interactions as interactions_{$status->getKey()}_count" => fn (Builder $query) => $query->whereBelongsTo($status, 'status'),
                ])),
            $type::make('care_team_count')
                ->label('Count of Care Team Members')
                ->counts('careTeam'),
            $type::make('care_team_count')
                ->label('Count of Care Team Members')
                ->counts('careTeam'),
            $type::make('service_requests_count')
                ->label('Count of Service Requests')
                ->counts('serviceRequests'),
            ...ServiceRequestStatus::all()->map(fn (ServiceRequestStatus $status): TextColumn | ExportColumn => $type::make("service_requests_{$status->getKey()}_count")
                ->label("Count of {$status->name} Service Requests")
                ->counts([
                    "serviceRequests as service_requests_{$status->getKey()}_count" => fn (Builder $query) => $query->whereBelongsTo($status, 'status'),
                ])),
            $type::make('asset_check_ins_count')
                ->label('Count of Returned Assets')
                ->counts('assetCheckIns'),
            $type::make('asset_check_outs_count')
                ->label('Count of Checked Out Assets')
                ->counts('assetCheckOuts'),
            $type::make('event_attendee_records_count')
                ->label('Count of Events')
                ->counts('eventAttendeeRecords'),
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
}
