<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages;

use AdvisingApp\Alert\Enums\StudentAlertStatus;
use AdvisingApp\Alert\Models\AlertConfiguration;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class ViewStudentAlerts extends Page implements HasTable
{
    use HasStudentHeader;
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Alerts';

    protected string $view = 'student-data-model::filament.resources.students.view-student-alerts';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (isset($parameters['record']) && ! static::getResource()::canView($parameters['record'])) {
            return false;
        }

        return parent::canAccess($parameters);
    }

    public function table(Table $table): Table
    {
        $student = $this->getRecord();

        assert($student instanceof Student);

        $sisid = $student->sisid;

        return $table
            ->query(
                AlertConfiguration::query()
                    ->select('alert_configurations.*')
                    ->selectRaw("
                        CASE
                            WHEN alert_configurations.is_enabled = false THEN 'disabled'
                            WHEN sa.sisid IS NOT NULL THEN 'active'
                            ELSE 'inactive'
                        END AS alert_status
                    ")
                    ->leftJoin('student_alerts AS sa', function (JoinClause $join) use ($sisid) {
                        $join->on('sa.alert_configuration_id', '=', 'alert_configurations.id')
                            ->where('sa.sisid', '=', $sisid);
                    })
            )
            ->columns([
                TextColumn::make('alert_name')
                    ->label('Alert Name')
                    ->state(fn (AlertConfiguration $record): string => $record->preset->getHandler()->getName())
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $ids = AlertConfiguration::all()
                            ->filter(fn (AlertConfiguration $config): bool => str_contains(
                                strtolower($config->preset->getHandler()->getName()),
                                strtolower($search)
                            ))
                            ->pluck('id');

                        return $query->whereIn('alert_configurations.id', $ids);
                    }),

                TextColumn::make('alert_description')
                    ->label('Alert Description')
                    ->state(fn (AlertConfiguration $record): string => $record->preset->getHandler()->getDescription())
                    ->wrap()
                    ->searchable(false),

                TextColumn::make('alert_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (AlertConfiguration $record): StudentAlertStatus => StudentAlertStatus::from($record->getAttribute('alert_status')))
                    ->color(fn (StudentAlertStatus $state): string => $state->getColor()),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(StudentAlertStatus::class)
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? '') {
                            StudentAlertStatus::Active->value => $query
                                ->where('alert_configurations.is_enabled', true)
                                ->whereNotNull('sa.sisid'),
                            StudentAlertStatus::Inactive->value => $query
                                ->where('alert_configurations.is_enabled', true)
                                ->whereNull('sa.sisid'),
                            StudentAlertStatus::Disabled->value => $query
                                ->where('alert_configurations.is_enabled', false),
                            default => $query,
                        };
                    })
                    ->default(StudentAlertStatus::Active->value),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
