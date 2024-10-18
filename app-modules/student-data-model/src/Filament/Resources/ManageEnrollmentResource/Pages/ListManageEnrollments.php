<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\ManageEnrollmentResource\Pages;

use AdvisingApp\StudentDataModel\Filament\Imports\EnrollmentImporter;
use AdvisingApp\StudentDataModel\Filament\Resources\ManageEnrollmentResource;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

class ListManageEnrollments extends ListRecords
{
    protected static string $resource = ManageEnrollmentResource::class;

    public function table(Table $table): Table
    {
        return $table
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
                // EditAction::make(),
                // ViewAction::make(),
                // DeleteAction::make()
                //     ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
            ])
            ->bulkActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make()
                //         ->modalDescription('Are you sure you wish to delete the selected record(s)? This action cannot be reversed')
                // ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(EnrollmentImporter::class)
                ->authorize('import', Enrollment::class),
        ];
    }
}
