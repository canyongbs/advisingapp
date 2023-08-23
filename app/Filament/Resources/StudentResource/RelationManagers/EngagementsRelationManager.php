<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Assist\Engagement\Models\Engagement;
use Filament\Forms\Components\TextInput;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use Filament\Resources\RelationManagers\RelationManager;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'engagements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject'),
                TextInput::make('description'),
                Repeater::make('deliverables')
                    ->relationship()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('channel')->required(),
                        TextInput::make('delivery_status'),
                        TextInput::make('delivered_at'),
                        TextInput::make('delvery_response'),
                        // TODO Need to make a custom view field in order to render what we'd like here...
                        // TextInput::make('delivery_status')
                        //     ->state(fn ($state): string => match ($state) {
                        //         EngagementDeliveryStatus::SUCCESSFUL => 'Successful',
                        //         EngagementDeliveryStatus::AWAITING => 'Awaiting Delivery',
                        //         EngagementDeliveryStatus::FAILED => 'Failed',
                        //     }),
                        // ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                        //     EngagementDeliveryStatus::SUCCESSFUL => 'success',
                        //     EngagementDeliveryStatus::AWAITING => 'info',
                        //     EngagementDeliveryStatus::FAILED => 'danger',
                        // }),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
