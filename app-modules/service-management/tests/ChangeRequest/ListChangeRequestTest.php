<?php

use function Tests\Helpers\testResourceRequiresPermissionForAccess;

use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

testResourceRequiresPermissionForAccess(
    resource: ChangeRequestResource::class,
    permission: 'change_request.view-any',
    method: 'index'
);
