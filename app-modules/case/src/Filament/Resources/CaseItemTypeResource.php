<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequestType;
use Assist\Case\Filament\Resources\CaseItemTypeResource\Pages;

class CaseItemTypeResource extends Resource
{
    protected static ?string $model = ServiceRequestType::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseItemTypes::route('/'),
            'create' => Pages\CreateCaseItemType::route('/create'),
            'view' => Pages\ViewCaseItemType::route('/{record}'),
            'edit' => Pages\EditCaseItemType::route('/{record}/edit'),
        ];
    }
}
