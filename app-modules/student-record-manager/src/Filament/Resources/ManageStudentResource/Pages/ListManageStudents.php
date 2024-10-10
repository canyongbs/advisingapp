<?php

namespace AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource\Pages;

use AdvisingApp\StudentRecordManager\Filament\Resources\ManageStudentResource;
use AdvisingApp\StudentRecordManager\Models\ManageableStudent;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                ]),
            ])
            ->headerActions([
                
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
