<?php

namespace Assist\Webhook\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Assist\Webhook\Models\InboundWebhook;
use Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages\EditInboundWebhook;
use Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages\ListInboundWebhooks;
use Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages\CreateInboundWebhook;

class InboundWebhookResource extends Resource
{
    protected static ?string $model = InboundWebhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInboundWebhooks::route('/'),
            'create' => CreateInboundWebhook::route('/create'),
            'edit' => EditInboundWebhook::route('/{record}/edit'),
        ];
    }
}
