<?php

namespace AdvisingApp\Application\Filament\Resources;

use Filament\Resources\Resource;
use AdvisingApp\Application\Models\ApplicationState;
use AdvisingApp\Application\Filament\Resources\ApplicationStateResource\Pages;

class ApplicationStateResource extends Resource
{
    protected static ?string $model = ApplicationState::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 18;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplicationStates::route('/'),
            'create' => Pages\CreateApplicationState::route('/create'),
            'view' => Pages\ViewApplicationState::route('/{record}'),
            'edit' => Pages\EditApplicationState::route('/{record}/edit'),
        ];
    }
}
