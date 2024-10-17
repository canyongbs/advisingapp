<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Actions\DeleteStudent;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentManageableImporter;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageStudentResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

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
                ViewAction::make(),
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
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? By deleting a student record, you will remove any related enrollment and program data, along with any related interactions, notes, etc. This action cannot be reversed.')
                        ->using(function ($records) {
                            foreach ($records as $record) {
                                app(DeleteStudent::class)->execute($record);
                            }
                        })
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(StudentManageableImporter::class)
                ->authorize('import', Student::class),
        ];
    }
}
