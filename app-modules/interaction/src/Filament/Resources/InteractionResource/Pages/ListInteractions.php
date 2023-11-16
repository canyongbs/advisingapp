<?php

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Carbon\CarbonInterface;
use App\Filament\Columns\IdColumn;
use App\Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\Interaction\Models\Interaction;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Interaction\Imports\InteractionsImporter;
use Assist\Interaction\Filament\Resources\InteractionResource;
use App\Filament\Columns\OpenSearch\TextColumn as OpenSearchTextColumn;

class ListInteractions extends ListRecords
{
    use FilterTableWithOpenSearch;

    protected static string $resource = InteractionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                OpenSearchTextColumn::make('campaign_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->campaign->name),
                OpenSearchTextColumn::make('driver_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->driver->name),
                OpenSearchTextColumn::make('division_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->division->name),
                OpenSearchTextColumn::make('outcome_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->outcome->name),
                OpenSearchTextColumn::make('relation_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->relation->name),
                OpenSearchTextColumn::make('status_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->status->name),
                OpenSearchTextColumn::make('type_name')
                    ->searchable()
                    ->getStateUsing(fn (Interaction $record) => $record->type->name),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime(),
                TextColumn::make('end_datetime')
                    ->label('End Time')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                    ->label('Duration'),
                OpenSearchTextColumn::make('subject')
                    ->searchable(),
                OpenSearchTextColumn::make('description')
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
