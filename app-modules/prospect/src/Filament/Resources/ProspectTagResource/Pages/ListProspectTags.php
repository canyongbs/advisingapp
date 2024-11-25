<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectTagResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\Prospect\Filament\Resources\ProspectTagResource;

class ListProspectTags extends ListRecords
{
    protected static string $resource = ProspectTagResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At'),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
