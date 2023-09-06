<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\ServiceManagement\Models\ServiceRequest;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\CreatedByRelationManager;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\AssignedToRelationManager;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\ServiceRequestUpdatesRelationManager;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationLabel = 'Service Request';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $label = 'Service Request';

    public static function getRelations(): array
    {
        return [
            ServiceRequestUpdatesRelationManager::class,
            RelationGroup::make('Related Users', [
                AssignedToRelationManager::class,
                CreatedByRelationManager::class,
            ]),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequests::route('/'),
            'create' => CreateServiceRequest::route('/create'),
            'view' => ViewServiceRequest::route('/{record}'),
            'edit' => EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
