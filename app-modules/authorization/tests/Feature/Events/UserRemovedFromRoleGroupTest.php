<?php

use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleRemovedFromRoleGroup;
use Assist\Authorization\Events\UserRemovedFromRoleGroup;
use Assist\Authorization\Listeners\HandleUserRemovedFromRoleGroup;

it('will fire when a user has been detached from a role group', function () {
    Event::fake();

    $roleGroup = RoleGroup::factory()
        ->hasUsers(1)
        ->create();

    $roleGroup->users()->detach($roleGroup->users->first());

    Event::assertDispatched(UserRemovedFromRoleGroup::class);
    Event::assertNotDispatched(RoleRemovedFromRoleGroup::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        UserRemovedFromRoleGroup::class,
        HandleUserRemovedFromRoleGroup::class
    );
});
