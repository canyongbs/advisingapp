<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\EditCaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\ViewCaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\CreateCaseItemStatus;
use Assist\Case\Filament\Resources\CaseItemStatusResource\Pages\ListCaseItemStatuses;

class CaseItemStatusResource extends Resource
{
    protected static ?string $model = CaseItemStatus::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
            'view' => ViewCaseItemStatus::route('/{record}'),
            'edit' => EditCaseItemStatus::route('/{record}/edit'),
        ];
    }
}
