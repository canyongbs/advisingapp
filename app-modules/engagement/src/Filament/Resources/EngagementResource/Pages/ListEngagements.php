<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Assist\Engagement\Models\Engagement;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Assist\Engagement\Filament\Resources\EngagementResource;

class ListEngagements extends ListRecords
{
    protected static string $resource = EngagementResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('user.name')
                    ->label('Created By'),
                TextColumn::make('subject'),
                TextColumn::make('body'),
                TextColumn::make('recipient.display_name')
                    ->label('Recipient')
                    ->getStateUsing(fn (Engagement $record) => $record->recipient->{$record->recipient::displayNameKey()}),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn (Engagement $record) => $record->hasBeenDelivered() === true),
                DeleteAction::make()
                    ->hidden(fn (Engagement $record) => $record->hasBeenDelivered() === true),
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
