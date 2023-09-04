<?php

namespace Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource;

class ListCaseItemPriorities extends ListRecords
{
    protected static string $resource = ServiceRequestPriorityResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
                TextColumn::make('case_items_count')
                    ->label('# of Case Items')
                    ->counts('caseItems')
                    ->sortable(),
            ])
            ->defaultSort('order')
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
            ])
            ->reorderable('order');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
