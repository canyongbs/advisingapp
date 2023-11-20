<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use App\Filament\Resources\RelationManagers\RelationManager;

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
                IdColumn::make(),
                TextColumn::make('channel'),
                IconColumn::make('delivery_status')
                    ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                        EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                        EngagementDeliveryStatus::Awaiting => 'heroicon-o-clock',
                        EngagementDeliveryStatus::Failed => 'heroicon-o-x-circle',
                    })
                    ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                        EngagementDeliveryStatus::Successful => 'success',
                        EngagementDeliveryStatus::Awaiting => 'info',
                        EngagementDeliveryStatus::Failed => 'danger',
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
            ]);
    }
}
