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

namespace AdvisingApp\StudentDataModel\Filament\Resources\ManageStudentResource\Pages;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Actions\DeleteStudent;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageStudentResource;

class ListManageStudents extends ListRecords
{
    protected static string $resource = ManageStudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email'),
                TextColumn::make('mobile'),
                TextColumn::make('phone'),
                TextColumn::make('sisid')
                    ->label('SIS ID'),
                TextColumn::make('otherid')
                    ->label('Other ID'),
            ])
            ->actions([
                ViewAction::make()
                    ->visible(function (Student $record) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->can('student_record_manager.*.view');
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Are you sure you wish to delete the selected record(s)? By deleting a student record, you will remove any related enrollment and program data, along with any related interactions, notes, etc. This action cannot be reversed.')
                    ->using(function ($record) {
                        app(DeleteStudent::class)->execute($record);

                        Notification::make()
                            ->title('Deleted successfully')
                            ->success()
                            ->body('The record and related entries have been successfully deleted.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? By deleting a student record, you will remove any related enrollment and program data, along with any related interactions, notes, etc. This action cannot be reversed.')
                        ->using(function ($records) {
                            foreach ($records as $record) {
                                app(DeleteStudent::class)->execute($record);
                            }
                        }),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->modalDescription('Import student records from a CSV file. Records with matched SIS IDs will be updated, while new records will be created.')
                ->importer(StudentImporter::class)
                ->authorize('import', Student::class),
        ];
    }
}
