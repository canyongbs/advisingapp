<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ServiceRequestTimeline;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUser;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUpdate;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestInteraction;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationLabel = 'Service Management';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 4;

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
