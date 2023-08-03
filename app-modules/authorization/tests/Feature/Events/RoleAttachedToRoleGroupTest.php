<?php

use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleAttachedToRoleGroup;
use Assist\Authorization\Events\UserAttachedToRoleGroup;
use Assist\Authorization\Listeners\HandleRoleAttachedToRoleGroup;

it('will fire when a role has been attached to a role group', function () {
    Event::fake();

    $role = Role::factory()
        ->create();

    $roleGroup = RoleGroup::factory()
        ->create();

    $roleGroup->roles()->attach($role);

    Event::assertDispatched(RoleAttachedToRoleGroup::class);
    Event::assertNotDispatched(UserAttachedToRoleGroup::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        RoleAttachedToRoleGroup::class,
        HandleRoleAttachedToRoleGroup::class
    );
});
