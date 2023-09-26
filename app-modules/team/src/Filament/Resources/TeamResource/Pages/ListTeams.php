<?php

namespace Assist\Team\Filament\Resources\TeamResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Team\Filament\Resources\TeamResource;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')
                    ->limit(50),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            CreateAction::make(),
        ];
    }
}
