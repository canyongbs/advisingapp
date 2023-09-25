<?php

namespace Assist\Notifications\Filament\Resources\SubscriptionResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageSubscriptions extends ManageRelatedRecords
{
    protected static string $relationship = 'subscriptions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Subscriptions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user]))
                    ->color('primary'),
                TextColumn::make('created_at'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
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
