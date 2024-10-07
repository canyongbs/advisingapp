<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentRecordManager\Filament\Imports\StudentManageableImporter;
use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource;
use AdvisingApp\StudentRecordManager\Models\ManageableStudent;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
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
                TextColumn::make(ManageableStudent::displayNameKey())
                    ->label('Name')
                    ->sortable(),
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(StudentManageableImporter::class)
                ->authorize('import', ManageableStudent::class),
        ];
    }
}
