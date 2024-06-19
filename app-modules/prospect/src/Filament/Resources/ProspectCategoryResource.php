<?php

namespace AdvisingApp\Prospect\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\ConstituentManagement;
use AdvisingApp\Prospect\Models\ProspectCategory;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource\Pages;

class ProspectCategoryResource extends Resource
{
    protected static ?string $model = ProspectCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Prospect Category';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = ConstituentManagement::class;

    protected static ?string $navigationGroup = 'Basic Needs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspectCategories::route('/'),
            'create' => Pages\CreateProspectCategory::route('/create'),
            'view' => Pages\ViewProspectCategory::route('{record}/'),
            'edit' => Pages\EditProspectCategory::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }
}
