<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers;

use AdvisingApp\StudentDataModel\Filament\Imports\ProgramImporter;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use Closure;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'programs';

    public function infolist(Schema $schema): Schema
    {
        $sisSystem = app(StudentInformationSystemSettings::class)->sis_system;

        return $schema
            ->components([
                TextEntry::make('sisid')
                    ->label('SISID'),
                TextEntry::make('division')
                    ->label('College')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDivision() ?? true),
                TextEntry::make('descr')
                    ->label('Program Name')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDescr() ?? true),
                TextEntry::make('acad_plan')
                    ->label('Program details')
                    ->placeholder('-')
                    ->state(fn (Program $record): ?string => $record->formatted_acad_plan)
                    ->visible($sisSystem?->hasProgramsAcadPlan() ?? true)
                    ->columnSpanFull(),
                TextEntry::make('foi')
                    ->label('Field of Interest')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsFoi() ?? true),
                TextEntry::make('cum_gpa')
                    ->label('Cumulative GPA')
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsCumGpa() ?? true),
                TextEntry::make('declare_dt')
                    ->label('Start Date')
                    ->dateTime()
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsDeclareDt() ?? true),
                TextEntry::make('change_dt')
                    ->label('Change Date')
                    ->dateTime()
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsChangeDt() ?? true),
                TextEntry::make('graduation_dt')
                    ->label('Graduation Date')
                    ->dateTime()
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsGraduationDt() ?? true),
                TextEntry::make('catalog_year')
                    ->label('Catalog Year')
                    ->placeholder('-'),
                TextEntry::make('conferred_dt')
                    ->label('Conferred Date')
                    ->dateTime()
                    ->placeholder('-')
                    ->visible($sisSystem?->hasProgramsConferredDt() ?? true),
            ]);
    }

    public function table(Table $table): Table
    {
        $sisSystem = app(StudentInformationSystemSettings::class)->sis_system;

        return $table
            ->recordTitleAttribute('descr')
            ->columns([
                TextColumn::make('division')
                    ->label('College')
                    ->visible($sisSystem?->hasProgramsDivision() ?? true),
                TextColumn::make('descr')
                    ->label('Name')
                    ->description(fn (Program $record): ?string => $record->formatted_acad_plan)
                    ->visible($sisSystem?->hasProgramsDescr() ?? true),
                TextColumn::make('foi')
                    ->label('Field of Interest')
                    ->visible($sisSystem?->hasProgramsFoi() ?? true),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA')
                    ->visible($sisSystem?->hasProgramsCumGpa() ?? true),
                TextColumn::make('declare_dt')
                    ->label('Start Date')
                    ->dateTime()
                    ->visible($sisSystem?->hasProgramsDeclareDt() ?? true),
                TextColumn::make('graduation_dt')
                    ->label('Graduation Date')
                    ->dateTime()
                    ->visible($sisSystem?->hasProgramsGraduationDt() ?? true),
                TextColumn::make('catalog_year')
                    ->label('Catalog Year')
                    ->placeholder('N/A'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->slideOver()
                    ->modalHeading('Program Information'),
                EditAction::make()
                    ->slideOver()
                    ->mutateRecordDataUsing(function (array $data): array {
                        $acadPlan = $data['acad_plan'] ?? [];

                        $data['acad_plan_major'] = implode(', ', $acadPlan['major'] ?? []);
                        $data['acad_plan_minor'] = implode(', ', $acadPlan['minor'] ?? []);

                        return $data;
                    })
                    ->using(function (array $data, Model $record): void {
                        $data['acad_plan'] = [
                            'major' => filled($data['acad_plan_major'] ?? null)
                                ? array_values(array_filter(array_map('trim', explode(',', $data['acad_plan_major']))))
                                : [],
                            'minor' => filled($data['acad_plan_minor'] ?? null)
                                ? array_values(array_filter(array_map('trim', explode(',', $data['acad_plan_minor']))))
                                : [],
                        ];

                        unset($data['acad_plan_major'], $data['acad_plan_minor']);

                        $record->update($data);
                    }),
                DeleteAction::make()
                    ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
                        ->action(function (Collection $records) {
                            $deletedCount = 0;
                            $notDeleteCount = 0;

                            /** @var Collection|Program[] $records */
                            foreach ($records as $record) {
                                /** @var Program $record */
                                $response = Gate::inspect('delete', $record);

                                if ($response->allowed()) {
                                    $record->delete();
                                    $deletedCount++;
                                } else {
                                    $notDeleteCount++;
                                }
                            }

                            $wasWere = fn ($count) => $count === 1 ? 'was' : 'were';

                            $notification = match (true) {
                                $deletedCount === 0 => [
                                    'title' => 'None deleted',
                                    'status' => 'danger',
                                    'body' => "{$notDeleteCount} {$wasWere($notDeleteCount)} skipped because you do not have permission to delete.",
                                ],
                                $deletedCount > 0 && $notDeleteCount > 0 => [
                                    'title' => 'Some deleted',
                                    'status' => 'warning',
                                    'body' => "{$deletedCount} {$wasWere($deletedCount)} deleted, but {$notDeleteCount} {$wasWere($notDeleteCount)} skipped because you do not have permission to delete.",
                                ],
                                default => [
                                    'title' => 'Deleted',
                                    'status' => 'success',
                                    'body' => null,
                                ],
                            };

                            Notification::make()
                                ->title($notification['title'])
                                ->{$notification['status']}()
                                ->body($notification['body'])
                                ->send();
                        })
                        ->visible(fn (): bool => app(ManageStudentConfigurationSettings::class)->is_enabled && auth()->user()->can('program.*.delete')),
                ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver()
                    ->using(function (array $data, RelationManager $livewire): Model {
                        $data['acad_plan'] = [
                            'major' => filled($data['acad_plan_major'] ?? null)
                                ? array_values(array_filter(array_map('trim', explode(',', $data['acad_plan_major']))))
                                : [],
                            'minor' => filled($data['acad_plan_minor'] ?? null)
                                ? array_values(array_filter(array_map('trim', explode(',', $data['acad_plan_minor']))))
                                : [],
                        ];

                        unset($data['acad_plan_major'], $data['acad_plan_minor']);
                        $student = $livewire->getOwnerRecord();
                        assert($student instanceof Student);

                        return $student->programs()->create($data);
                    }),
                ImportAction::make()
                    ->importer(ProgramImporter::class)
                    ->authorize('import', Program::class)
                    ->options(['sisid' => $this->getOwnerRecord()->getKey()]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('acad_career')
                    ->string()
                    ->maxLength(255)
                    ->label('Academic Career'),
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->label('College'),
                TextInput::make('acad_plan_major')
                    ->label('Major(s)')
                    ->helperText('Comma-separated, e.g. Computer Science, Mathematics')
                    ->rule(function (): Closure {
                        return function (string $attribute, mixed $value, Closure $fail): void {
                            if (blank($value)) {
                                return;
                            }

                            foreach (array_map('trim', explode(',', $value)) as $item) {
                                if (blank($item)) {
                                    $fail('Each major must not be empty.');

                                    return;
                                }

                                if (str_contains($item, ':')) {
                                    $fail('Majors must not contain a colon.');

                                    return;
                                }
                            }
                        };
                    }),
                TextInput::make('acad_plan_minor')
                    ->label('Minor(s)')
                    ->helperText('Comma-separated, e.g. Philosophy')
                    ->rule(function (): Closure {
                        return function (string $attribute, mixed $value, Closure $fail): void {
                            if (blank($value)) {
                                return;
                            }

                            foreach (array_map('trim', explode(',', $value)) as $item) {
                                if (blank($item)) {
                                    $fail('Each minor must not be empty.');

                                    return;
                                }

                                if (str_contains($item, ':')) {
                                    $fail('Minors must not contain a colon.');

                                    return;
                                }
                            }
                        };
                    }),
                TextInput::make('prog_status')
                    ->label('Program Status')
                    ->default('AC'),
                TextInput::make('cum_gpa')
                    ->label('Cumulative GPA')
                    ->numeric(),
                TextInput::make('semester')
                    ->label('Semester')
                    ->string()
                    ->maxLength(255),
                TextInput::make('descr')
                    ->label('Program Name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('foi')
                    ->label('Field of Interest'),
                DateTimePicker::make('change_dt')
                    ->label('Change Date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('declare_dt')
                    ->label('Declare Date')
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('graduation_dt')
                    ->label('Graduation Date')
                    ->closeOnDateSelection(),
                TextInput::make('catalog_year')
                    ->label('Catalog Year'),
            ]);
    }
}
