<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Prospect\Filament\Resources\ProspectStatusResource\Pages;

class ProspectStatusResource extends Resource
{
    protected static ?string $model = ProspectStatus::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            'index' => Pages\ListProspectStatuses::route('/'),
            'create' => Pages\CreateProspectStatus::route('/create'),
            'view' => Pages\ViewProspectStatus::route('/{record}'),
            'edit' => Pages\EditProspectStatus::route('/{record}/edit'),
        ];
    }
}
