<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class ListInboundWebhooks extends ListRecords
{
    protected static string $resource = InboundWebhookResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('source')
                    ->label('Source'),
                TextColumn::make('event'),
                TextColumn::make('url'),
                TextColumn::make('payload'),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ]);
    }
}
