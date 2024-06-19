<?php

namespace AdvisingApp\Prospect\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Prospect\Models\ProspectProgram;
use App\Filament\Clusters\ConstituentManagement;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AdvisingApp\Prospect\Filament\Resources\ProspectProgramResource\Pages;

class ProspectProgramResource extends Resource
{
    protected static ?string $model = ProspectProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'Prospect Program';

    protected static ?int $navigationSort = 2;

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
            'index' => Pages\ListProspectPrograms::route('/'),
            'create' => Pages\CreateProspectProgram::route('/create'),
            'view' => Pages\ViewProspectProgram::route('/{record}'),
            'edit' => Pages\EditProspectProgram::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
