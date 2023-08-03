<?php

use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleGroupRolePivotDeleted;
use Assist\Authorization\Events\RoleGroupUserPivotDeleted;
use Assist\Authorization\Listeners\HandleRoleGroupRolePivotDeleted;

it('will fire when a role has been detached from a role group', function () {
    Event::fake();

    $roleGroup = RoleGroup::factory()
        ->hasRoles(1)
        ->hasUsers(1)
        ->create();

    $roleGroup->roles()->detach($roleGroup->roles->first());

    Event::assertDispatched(RoleGroupRolePivotDeleted::class);
    Event::assertNotDispatched(RoleGroupUserPivotDeleted::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        RoleGroupRolePivotDeleted::class,
        HandleRoleGroupRolePivotDeleted::class
    );
});
