<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Events\RoleGroupRolePivotSaved;
use Assist\Authorization\Events\RoleGroupUserPivotSaved;
use Assist\Authorization\Listeners\HandleRoleGroupUserPivotSaved;

it('will fire when a user has been attached to a role group', function () {
    Event::fake();

    $user = User::factory()
        ->create();

    $roleGroup = RoleGroup::factory()
        ->create();

    $roleGroup->users()->attach($user);

    Event::assertDispatched(RoleGroupUserPivotSaved::class);
    Event::assertNotDispatched(RoleGroupRolePivotSaved::class);
});

it('will be handled by the correct listener', function () {
    Event::fake();

    Event::assertListening(
        RoleGroupUserPivotSaved::class,
        HandleRoleGroupUserPivotSaved::class
    );
});
