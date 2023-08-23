<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use Filament\Resources\RelationManagers\RelationManager;

class EngagementDeliverablesRelationManager extends RelationManager
{
    protected static string $relationship = 'engagementDeliverables';

    protected static ?string $recordTitleAttribute = 'channel';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('channel')
                    ->label('How would you like to send this engagement?')
                    ->translateLabel()
                    ->options(EngagementDeliveryMethod::class)
                    ->disableOptionWhen(fn (string $value) => $this->ownerRecord->deliverables->where('channel', EngagementDeliveryMethod::from($value))->count() > 0)
                    ->validationAttribute('Delivery Method')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('channel')
            ->columns([
                TextColumn::make('channel'),
                IconColumn::make('delivery_status')
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
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
