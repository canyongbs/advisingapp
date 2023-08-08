<?php

namespace Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Assist\Case\Models\CaseItemStatus;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Prospect\Filament\Resources\ProspectStatusResource;

class ListProspectStatuses extends ListRecords
{
    protected static string $resource = ProspectStatusResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (CaseItemStatus $caseItemStatus) => $caseItemStatus->color),
                TextColumn::make('case_items_count')
                    ->label('# of Case Items')
                    ->counts('caseItems')
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
