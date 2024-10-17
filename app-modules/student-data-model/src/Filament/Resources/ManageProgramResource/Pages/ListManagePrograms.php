<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource\Pages;

use AdvisingApp\StudentDataModel\Filament\Imports\ManageableProgramImporter;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageProgramResource;
use AdvisingApp\StudentDataModel\Models\Program;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ListManagePrograms extends ListRecords
{
    protected static string $resource = ManageProgramResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('otherid')
                    ->label('STUID'),
                TextColumn::make('division')
                    ->label('College'),
                TextColumn::make('descr')
                    ->label('Program'),
                TextColumn::make('foi')
                    ->label('Field of Interest'),
                TextColumn::make('cum_gpa')
                    ->label('Cumulative GPA'),
                TextColumn::make('declare_dt')
                    ->label('Start Date'),
            ])->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(ManageableProgramImporter::class)
                ->authorize('import', Program::class),
        ];
    }
}
