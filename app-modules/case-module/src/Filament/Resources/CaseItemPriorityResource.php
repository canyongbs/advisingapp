<?php

namespace Assist\CaseModule\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Assist\CaseModule\Models\CaseItemPriority;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource\Pages\EditCaseItemPriority;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource\Pages\ViewCaseItemPriority;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource\Pages\CreateCaseItemPriority;
use Assist\CaseModule\Filament\Resources\CaseItemPriorityResource\Pages\ListCaseItemPriorities;

class CaseItemPriorityResource extends Resource
{
    protected static ?string $model = CaseItemPriority::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('order')
                    ->numeric()
                    ->label('Priority Order')
                    ->required()
                    ->disabledOn('edit'),
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
                Tables\Columns\TextColumn::make('order')
                    ->label('Priority Order')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->filters([
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
            ->reorderable('order');
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseItemPriorities::route('/'),
            'create' => CreateCaseItemPriority::route('/create'),
            'view' => ViewCaseItemPriority::route('/{record}'),
            'edit' => EditCaseItemPriority::route('/{record}/edit'),
        ];
    }
}
