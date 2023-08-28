<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Assist\Engagement\Models\Engagement;
use Filament\Tables\Actions\CreateAction;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use Filament\Resources\RelationManagers\RelationManager;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'engagements';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),

                Fieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('description'),
                    ]),

                RepeatableEntry::make('deliverables')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('channel'),
                        IconEntry::make('delivery_status')
                            ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::SUCCESSFUL => 'heroicon-o-check-circle',
                                EngagementDeliveryStatus::AWAITING => 'heroicon-o-clock',
                                EngagementDeliveryStatus::FAILED => 'heroicon-o-x-circle',
                            })
                            ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::SUCCESSFUL => 'success',
                                EngagementDeliveryStatus::AWAITING => 'info',
                                EngagementDeliveryStatus::FAILED => 'danger',
                            }),
                        TextEntry::make('delivered_at'),
                        TextEntry::make('delivery_response'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('subject'),
                TextColumn::make('description'),
                TextColumn::make('channels')
                    ->label('Delivery Channels')
                    ->state(function (Engagement $record) {
                        return $record->deliverables->pluck('channel')->map(function ($channel) {
                            return $channel->name;
                        })->implode(', ');
                    }),
            ])
            ->filters([
            ])
            ->headerActions([
                // TODO Enable Engagement creation from the StudentResource
                // CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->emptyStateActions([
                // TODO Enable Engagement creation from the StudentResource
                // CreateAction::make(),
            ]);
    }
}
