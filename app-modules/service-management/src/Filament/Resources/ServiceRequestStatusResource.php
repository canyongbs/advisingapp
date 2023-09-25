<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\EditServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\ViewServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\CreateServiceRequestStatus;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestStatusResource\Pages\ListServiceRequestStatuses;

class ServiceRequestStatusResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = ServiceRequestStatus::class;

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
