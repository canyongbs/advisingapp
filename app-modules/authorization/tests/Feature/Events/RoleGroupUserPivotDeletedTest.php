<?php

use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleGroupRolePivotDeleted;
use Assist\Authorization\Events\RoleGroupUserPivotDeleted;
use Assist\Authorization\Listeners\HandleRoleGroupUserPivotDeleted;

it('will fire when a user has been detached from a role group', function () {
    Event::fake();

    $roleGroup = RoleGroup::factory()
        ->hasUsers(1)
        ->create();

    $roleGroup->users()->detach($roleGroup->users->first());

    Event::assertDispatched(RoleGroupUserPivotDeleted::class);
    Event::assertNotDispatched(RoleGroupRolePivotDeleted::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        RoleGroupUserPivotDeleted::class,
        HandleRoleGroupUserPivotDeleted::class
    );
});
