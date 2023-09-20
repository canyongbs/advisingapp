<?php

namespace Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\Consent\Filament\Resources\ConsentAgreementResource;

class ListConsentAgreements extends ListRecords
{
    protected static string $resource = ConsentAgreementResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('title'),
            ])
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
