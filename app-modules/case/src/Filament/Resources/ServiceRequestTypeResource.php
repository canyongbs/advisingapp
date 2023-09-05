<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequestType;
use Assist\Case\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestType;
use Assist\Case\Filament\Resources\ServiceRequestTypeResource\Pages\ViewServiceRequestType;
use Assist\Case\Filament\Resources\ServiceRequestTypeResource\Pages\ListServiceRequestTypes;
use Assist\Case\Filament\Resources\ServiceRequestTypeResource\Pages\CreateServiceRequestType;

class ServiceRequestTypeResource extends Resource
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
            'index' => ListServiceRequestTypes::route('/'),
            'create' => CreateServiceRequestType::route('/create'),
            'view' => ViewServiceRequestType::route('/{record}'),
            'edit' => EditServiceRequestType::route('/{record}/edit'),
        ];
    }
}
