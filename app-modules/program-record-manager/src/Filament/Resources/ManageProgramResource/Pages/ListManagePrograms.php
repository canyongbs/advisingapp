<?php

namespace AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource\Pages;

use AdvisingApp\ProgramRecordManager\Filament\Imports\ManageableProgramImporter;
use AdvisingApp\ProgramRecordManager\Filament\Resources\ManageProgramResource;
use AdvisingApp\ProgramRecordManager\Models\ManageableProgram;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions;
use Filament\Actions\ImportAction;
use Filament\Infolists\Components\TextEntry;
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
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->importer(ManageableProgramImporter::class)
                ->authorize('import', ManageableProgram::class),
        ];
    }
}
