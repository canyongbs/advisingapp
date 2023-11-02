<?php

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ServiceRequestTimeline;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUser;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestInteraction;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationLabel = 'Service Management';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Service Management';

    protected static ?string $pluralLabel = 'Service Management';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewServiceRequest::class,
            EditServiceRequest::class,
            ManageServiceRequestUser::class,
            ManageServiceRequestUpdate::class,
            ManageServiceRequestInteraction::class,
            ServiceRequestTimeline::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequests::route('/'),
            'manage-users' => ManageServiceRequestUser::route('/{record}/users'),
            'manage-service-request-updates' => ManageServiceRequestUpdate::route('/{record}/updates'),
            'manage-interactions' => ManageServiceRequestInteraction::route('/{record}/interactions'),
            'create' => CreateServiceRequest::route('/create'),
            'view' => ViewServiceRequest::route('/{record}'),
            'edit' => EditServiceRequest::route('/{record}/edit'),
            'timeline' => ServiceRequestTimeline::route('/{record}/timeline'),
        ];
    }
}
