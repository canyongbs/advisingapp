<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ViewServiceRequestType;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ListServiceRequestTypes;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\CreateServiceRequestType;

class ServiceRequestTypeResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = ServiceRequestType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequestTypes::route('/'),
            'create' => CreateServiceRequestType::route('/create'),
            'view' => ViewServiceRequestType::route('/{record}'),
            'edit' => EditServiceRequestType::route('/{record}/edit'),
        ];
    }
}
