<?php

namespace Assist\Interaction\Filament\InteractionResource\RelationManagers;

use Filament\Tables\Table;
use Carbon\CarbonInterface;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;

class HasManyMorphedInteractionsRelationManager extends RelationManager
{
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),

                Fieldset::make('Details')
                    ->schema([
                        TextEntry::make('campaign.name'),
                        TextEntry::make('driver.name'),
                        TextEntry::make('institution.name'),
                        TextEntry::make('outcome.name'),
                        TextEntry::make('relation.name'),
                        TextEntry::make('status.name'),
                        TextEntry::make('type.name'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        TextEntry::make('start_datetime')
                            ->label('Start Time')
                            ->dateTime(),
                        TextEntry::make('end_datetime')
                            ->label('End Time')
                            ->dateTime(),
                        TextEntry::make('start_datetime')
                            ->label('Duration')
                            ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6)),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('description'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('campaign.name'),
                TextColumn::make('driver.name'),
                TextColumn::make('institution.name'),
                TextColumn::make('outcome.name'),
                TextColumn::make('relation.name'),
                TextColumn::make('status.name'),
                TextColumn::make('type.name'),
                TextColumn::make('start_datetime')
                    ->label('Start Time')
                    ->dateTime(),
                TextColumn::make('end_datetime')
                    ->label('End Time')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->state(fn ($record) => $record->end_datetime->diffForHumans($record->start_datetime, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                    ->label('Duration'),
                TextColumn::make('subject'),
                TextColumn::make('description'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
