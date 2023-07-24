<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\CaseItem;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CaseItemResource\Pages;

class CaseItemResource extends Resource
{
    protected static ?string $model = CaseItem::class;

    protected static ?string $navigationGroup = 'Cases';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

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
                Tables\Columns\TextColumn::make('casenumber')
                    ->label('Case #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority.name')
                    ->label('Priority')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByPowerJoins('priority.order', $direction);
                    }),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (CaseItem $caseItem) => $caseItem->state->color),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->relationship('priority', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCaseItems::route('/'),
            'create' => Pages\CreateCaseItem::route('/create'),
            'view' => Pages\ViewCaseItem::route('/{record}'),
            'edit' => Pages\EditCaseItem::route('/{record}/edit'),
        ];
    }
}
