<?php

namespace Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

use Filament\Actions;
use Filament\Tables\Table;
use Assist\Case\Models\CaseItemType;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Case\Filament\Resources\CaseItemTypeResource;

class ListCaseItemTypes extends ListRecords
{
    protected static string $resource = CaseItemTypeResource::class;

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

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        if (filled($search = $this->getTableSearch())) {
            // TODO: This seems very slow and only finds exact matches. Need to investigate.
            $query->whereIn('id', CaseItemType::search($search . '*')->keys());
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
