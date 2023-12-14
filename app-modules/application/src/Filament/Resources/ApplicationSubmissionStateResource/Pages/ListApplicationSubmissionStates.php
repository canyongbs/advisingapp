<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationStateSubmissionResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStateResource;

class ListApplicationSubmissionStates extends ListRecords
{
    protected static string $resource = ApplicationSubmissionStateResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classification')
                    ->label('Classification')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (ApplicationSubmissionState $applicationState) => $applicationState->color->value),
                TextColumn::make('applications_count')
                    ->label('# of Applications')
                    ->counts('applications')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
