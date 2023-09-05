<?php

namespace Assist\Case\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Case\Models\ServiceRequest;
use Filament\Resources\RelationManagers\RelationGroup;
use Assist\Case\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use Assist\Case\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use Assist\Case\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use Assist\Case\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;
use Assist\Case\Filament\Resources\ServiceRequestResource\RelationManagers\CreatedByRelationManager;
use Assist\Case\Filament\Resources\ServiceRequestResource\RelationManagers\AssignedToRelationManager;
use Assist\Case\Filament\Resources\ServiceRequestResource\RelationManagers\ServiceRequestUpdatesRelationManager;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 1;

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
