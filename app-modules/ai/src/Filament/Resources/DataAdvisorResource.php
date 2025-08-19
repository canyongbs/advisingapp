<?php

namespace AdvisingApp\Ai\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use AdvisingApp\Ai\Models\DataAdvisor;
use Filament\Tables\Actions\EditAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages;
use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\RelationManagers;
use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages\EditDataAdvisor;
use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages\ListDataAdvisors;
use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages\CreateDataAdvisor;

class DataAdvisorResource extends Resource
{
    protected static ?string $model = DataAdvisor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?int $navigationSort = 35;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDataAdvisors::route('/'),
            'create' => CreateDataAdvisor::route('/create'),
            'edit' => EditDataAdvisor::route('/{record}/edit'),
        ];
    }
}
