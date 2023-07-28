<?php

namespace Assist\Case\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Enums\ColumnColorOptions;
use Filament\Forms\Components\Select;
use Assist\Case\Models\CaseItemStatus;
use Filament\Forms\Components\TextInput;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\EditCaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\CreateCaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\ListCaseItemStatuses;

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
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->color(fn (CaseItemStatus $caseItemStatus) => $caseItemStatus->color),
                Tables\Columns\TextColumn::make('case_items_count')
                    ->label('# of Case Items')
                    ->counts('caseItems')
                    ->sortable(),
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
            'index' => ListCaseItemStatuses::route('/'),
            'create' => CreateCaseItemStatus::route('/create'),
            'edit' => EditCaseItemStatus::route('/{record}/edit'),
        ];
    }
}
