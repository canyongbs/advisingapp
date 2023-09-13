<?php

namespace Assist\Alert\Filament\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Assist\Alert\Enums\AlertSeverity;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class AlertsRelationManager extends RelationManager
{
    protected static string $relationship = 'alerts';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('description'),
                TextEntry::make('severity')
                    ->formatStateUsing(fn (AlertSeverity $state): string => ucfirst($state->value)),
                TextEntry::make('suggested_intervention'),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->required(),
                Select::make('severity')
                    ->options(AlertSeverity::class)
                    ->enum(AlertSeverity::class),
                Textarea::make('suggested_intervention')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('description')
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable()
                    ->formatStateUsing(fn (AlertSeverity $state): string => ucfirst($state->value)),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
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
