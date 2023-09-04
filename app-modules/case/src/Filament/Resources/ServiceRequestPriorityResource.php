<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\EditCaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\ViewCaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\CreateCaseItemPriority;
use Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages\ListCaseItemPriorities;

class ServiceRequestPriorityResource extends Resource
{
    protected static ?string $model = ServiceRequestPriority::class;

    protected static ?string $navigationGroup = 'Field Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';

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
