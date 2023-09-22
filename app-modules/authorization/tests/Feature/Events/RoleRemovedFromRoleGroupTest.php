<?php

use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleRemovedFromRoleGroup;
use Assist\Authorization\Events\UserRemovedFromRoleGroup;
use Assist\Authorization\Listeners\HandleRoleRemovedFromRoleGroup;

it('will fire when a role has been detached from a role group', function () {
    Event::fake();

    $roleGroup = RoleGroup::factory()
        ->hasRoles(1)
        ->hasUsers(1)
        ->create();

    $roleGroup->roles()->detach($roleGroup->roles->first());

    Event::assertDispatched(RoleRemovedFromRoleGroup::class);
    Event::assertNotDispatched(UserRemovedFromRoleGroup::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        RoleRemovedFromRoleGroup::class,
        HandleRoleRemovedFromRoleGroup::class
    );
});
