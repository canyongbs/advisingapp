<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Assist\Case\Models\CaseUpdate;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Assist\Case\Enums\CaseUpdateDirection;
use Assist\Case\Filament\Resources\CaseUpdateResource;
use Filament\Resources\RelationManagers\RelationManager;

class CaseUpdatesRelationManager extends RelationManager
{
    protected static string $relationship = 'caseUpdates';

    protected static ?string $recordTitleAttribute = 'update';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('update')
                    ->label('Update')
                    ->translateLabel()
                    ->words(6),
                IconColumn::make('internal')
                    ->boolean(),
                TextColumn::make('direction')
                    ->icon(fn (CaseUpdateDirection $state): string => match ($state) {
                        CaseUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
                        CaseUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
                    })
                    ->formatStateUsing(fn (CaseUpdateDirection $state): string => Str::ucfirst($state->value)),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (CaseUpdate $caseUpdate) => CaseUpdateResource::getUrl('view', ['record' => $caseUpdate])),
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
}
