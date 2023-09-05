<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource\Pages\EditServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource\Pages\ViewServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource\Pages\ListServiceRequestPriorities;

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
            'index' => ListServiceRequestPriorities::route('/'),
            'create' => CreateServiceRequestPriority::route('/create'),
            'view' => ViewServiceRequestPriority::route('/{record}'),
            'edit' => EditServiceRequestPriority::route('/{record}/edit'),
        ];
    }
}
