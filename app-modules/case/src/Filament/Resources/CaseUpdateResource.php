<?php

namespace Assist\Case\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Assist\Case\Models\CaseUpdate;
use Assist\Case\Enums\CaseUpdateDirection;
use Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

class CaseUpdateResource extends Resource
{
    protected static ?string $model = CaseUpdate::class;

    protected static ?string $navigationGroup = 'Cases';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('case.respondent.full')
                    ->label('Respondent')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('case.respondent.sisid')
                    ->label('SIS ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('case.respondent.otherid')
                    ->label('Other ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('case.casenumber')
                    ->label('Case')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('internal')
                    ->boolean()
                    ->label('Internal'),
                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->formatStateUsing(fn (CaseUpdateDirection $state): string => Str::ucfirst($state->value)),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('internal')
                    ->label('Internal')
                    ->translateLabel(),
                Tables\Filters\SelectFilter::make('direction')
                    ->label('Direction')
                    ->translateLabel()
                    ->options(
                        collect(CaseUpdateDirection::cases())
                            ->mapWithKeys(fn (CaseUpdateDirection $direction) => [$direction->value => $direction->name])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCaseUpdates::route('/'),
            'create' => Pages\CreateCaseUpdate::route('/create'),
            'view' => Pages\ViewCaseUpdate::route('/{record}'),
            'edit' => Pages\EditCaseUpdate::route('/{record}/edit'),
        ];
    }
}
