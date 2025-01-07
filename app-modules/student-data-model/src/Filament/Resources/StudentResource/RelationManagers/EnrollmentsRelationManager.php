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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use AdvisingApp\StudentDataModel\Filament\Imports\EnrollmentImporter;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema(
                [
                    TextEntry::make('sisid')
                        ->label('SISID'),
                    TextEntry::make('division')
                        ->label('College'),
                    TextEntry::make('class_nbr')
                        ->label('Course'),
                    TextEntry::make('crse_grade_off')
                        ->label('Grade'),
                    TextEntry::make('unt_taken')
                        ->label('Attempted'),
                    TextEntry::make('unt_earned')
                        ->label('Earned'),
                    TextEntry::make('section')
                        ->label('Section')
                        ->default('N/A'),
                    TextEntry::make('name')
                        ->label('Name')
                        ->default('N/A'),
                    TextEntry::make('department')
                        ->label('Department')
                        ->default('N/A'),
                    TextEntry::make('faculty_name')
                        ->label('Faculty Name')
                        ->default('N/A'),
                    TextEntry::make('faculty_email')
                        ->label('Faculty Email')
                        ->default('N/A'),
                    TextEntry::make('semester_code')
                        ->label('Semester Code')
                        ->default('N/A'),
                    TextEntry::make('semester_name')
                        ->label('Semester Name')
                        ->default('N/A'),
                    TextEntry::make('start_date')
                        ->label('Start Date')
                        ->default('N/A'),
                    TextEntry::make('end_date')
                        ->label('End Date')
                        ->default('N/A'),
                ]
            );
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('division')
            ->columns([
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('class_nbr')
                    ->label('Course'),
                TextColumn::make('crse_grade_off')
                    ->label('Grade'),
                TextColumn::make('unt_taken')
                    ->label('Attempted'),
                TextColumn::make('unt_earned')
                    ->label('Earned'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
                        ->action(function (Collection $records) {
                            $deletedCount = 0;
                            $notDeleteCount = 0;

                            /** @var Collection|Enrollment[] $records */
                            foreach ($records as $record) {
                                /** @var Program $record */
                                $response = Gate::inspect('delete', $record);

                                if ($response->allowed()) {
                                    $record->delete();
                                    $deletedCount++;
                                } else {
                                    $notDeleteCount++;

                                    continue;
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
                        }),
                ]),
            ])
            ->headerActions([
                CreateAction::make(),
                ImportAction::make()
                    ->importer(EnrollmentImporter::class)
                    ->authorize('import', Enrollment::class)
                    ->options(['sisid' => $this->getOwnerRecord()->getKey()]),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('division')
                    ->string()
                    ->maxLength(255)
                    ->label('Division'),
                TextInput::make('class_nbr')
                    ->label('Class NBR')
                    ->string()
                    ->maxLength(255),
                TextInput::make('crse_grade_off')
                    ->string()
                    ->maxLength(255)
                    ->label('CRSE grade off'),
                TextInput::make('unt_taken')
                    ->label('UNT taken')
                    ->numeric(),
                TextInput::make('unt_earned')
                    ->label('UNT earned')
                    ->numeric(),
                DateTimePicker::make('last_upd_dt_stmp')
                    ->label('Last UPD date STMP')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                TextInput::make('section')
                    ->label('Section')
                    ->string()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('department')
                    ->label('Department')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_name')
                    ->label('Faculty name')
                    ->string()
                    ->maxLength(255),
                TextInput::make('faculty_email')
                    ->label('Faculty email')
                    ->email(),
                TextInput::make('semester_code')
                    ->label('Semester code')
                    ->string()
                    ->maxLength(255),
                TextInput::make('semester_name')
                    ->label('Semester name')
                    ->string()
                    ->maxLength(255),
                DateTimePicker::make('start_date')
                    ->label('Start date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
                DateTimePicker::make('end_date')
                    ->label('End date')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('Y-m-d H:i:s'),
            ]);
    }
}
