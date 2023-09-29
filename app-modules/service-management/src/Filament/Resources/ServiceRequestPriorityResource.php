<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\ServiceManagement\Models\ServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages\EditServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages\ViewServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages\CreateServiceRequestPriority;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages\ListServiceRequestPriorities;

class ServiceRequestPriorityResource extends Resource
{
    protected static ?string $model = ServiceRequestPriority::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?string $navigationGroup = 'Product Settings';

    protected static ?int $navigationSort = 4;

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
