<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class ListCaseloads extends ListRecords
{
    protected static string $resource = CaseloadResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('model')
                    ->label('Population')
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable()
                    ->hidden(function (Table $table) {
                        return $table->getFilter('my_caseloads')->getState()['isActive'];
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                Filter::make('my_caseloads')
                    ->label('My Caseloads')
                    ->query(
                        fn ($query) => $query->where('user_id', auth()->id())
                    )
                    ->default(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
