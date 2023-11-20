<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleAttachedToRoleGroup;
use Assist\Authorization\Events\UserAttachedToRoleGroup;
use Assist\Authorization\Listeners\HandleUserAttachedToRoleGroup;

it('will fire when a user has been attached to a role group', function () {
    Event::fake();

    $user = User::factory()
        ->create();

    $roleGroup = RoleGroup::factory()
        ->create();

    $roleGroup->users()->attach($user);

    Event::assertDispatched(UserAttachedToRoleGroup::class);
    Event::assertNotDispatched(RoleAttachedToRoleGroup::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        UserAttachedToRoleGroup::class,
        HandleUserAttachedToRoleGroup::class
    );
});
