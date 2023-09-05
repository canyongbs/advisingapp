<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource\Pages\EditServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource\Pages\ViewServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource\Pages\CreateServiceRequestStatus;
use Assist\Case\Filament\Resources\ServiceRequestStatusResource\Pages\ListServiceRequestStatuses;

class ServiceRequestStatusResource extends Resource
{
    protected static ?string $model = ServiceRequestStatus::class;

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
            'index' => ListServiceRequestStatuses::route('/'),
            'create' => CreateServiceRequestStatus::route('/create'),
            'view' => ViewServiceRequestStatus::route('/{record}'),
            'edit' => EditServiceRequestStatus::route('/{record}/edit'),
        ];
    }
}
