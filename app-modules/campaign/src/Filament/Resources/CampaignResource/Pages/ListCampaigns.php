<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Assist\Campaign\Models\Campaign;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Assist\Campaign\Filament\Resources\CampaignResource;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
                TextColumn::make('caseload.name'),
                TextColumn::make('execute_at')
                    ->dateTime(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
                DeleteAction::make()
                    ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
            ])
            ->bulkActions([
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
