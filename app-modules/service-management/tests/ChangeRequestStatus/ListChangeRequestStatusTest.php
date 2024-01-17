<?php

use function Tests\Helpers\testResourceRequiresPermissionForAccess;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestStatusResource;

testResourceRequiresPermissionForAccess(
    resource: ChangeRequestStatusResource::class,
    permission: 'change_request_status.view-any',
    method: 'index'
);
