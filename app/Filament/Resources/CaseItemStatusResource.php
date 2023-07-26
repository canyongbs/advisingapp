<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CaseItemStatus;
use Filament\Resources\Resource;
use App\Enums\ColumnColorOptions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\CaseItemStatusResource\Pages;

class CaseItemStatusResource extends Resource
{
    protected static ?string $model = CaseItemStatus::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->translateLabel()
                    ->required(),
                Select::make('color')
                    ->label('Color')
                    ->translateLabel()
                    ->options(ColumnColorOptions::class)
                    ->required()
                    ->enum(ColumnColorOptions::class),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (CaseItemStatus $caseItemStatus) => $caseItemStatus->color),
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
            'index' => Pages\ListCaseItemStatuses::route('/'),
            'create' => Pages\CreateCaseItemStatus::route('/create'),
            'edit' => Pages\EditCaseItemStatus::route('/{record}/edit'),
        ];
    }
}
