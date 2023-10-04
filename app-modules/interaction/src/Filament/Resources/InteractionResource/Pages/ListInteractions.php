<?php

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Carbon\CarbonInterface;
use App\Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;
use Assist\Interaction\Models\Interaction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Interaction\Imports\InteractionsImporter;
use Assist\Interaction\Filament\Resources\InteractionResource;

class ListInteractions extends ListRecords
{
    protected static string $resource = InteractionResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('campaign.name')
                    ->searchable(),
                TextColumn::make('driver.name')
                    ->searchable(),
                TextColumn::make('division.name')
                    ->searchable(),
                TextColumn::make('outcome.name')
                    ->searchable(),
                TextColumn::make('relation.name')
                    ->searchable(),
                TextColumn::make('status.name')
                    ->searchable(),
                TextColumn::make('type.name')
                    ->searchable(),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime(),
                TextColumn::make('end_datetime')
                    ->label('End Time')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                    ->label('Duration'),
                TextColumn::make('subject')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(InteractionsImporter::class)
                ->authorize('import', Interaction::class),
            Actions\CreateAction::make(),
        ];
    }
}
