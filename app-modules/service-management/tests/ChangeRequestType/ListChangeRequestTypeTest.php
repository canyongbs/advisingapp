<?php

use function Tests\Helpers\testResourceRequiresPermissionForAccess;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestTypeResource;

testResourceRequiresPermissionForAccess(
    resource: ChangeRequestTypeResource::class,
    permission: 'change_request_type.view-any',
    method: 'index'
);
