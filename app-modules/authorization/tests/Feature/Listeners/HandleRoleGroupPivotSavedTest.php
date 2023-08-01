<?php

namespace Assist\Authorization\Tests\Feature\Listeners;

use Tests\TestCase;

class HandleRoleGroupPivotSavedTest extends TestCase
{
    // when a role group is saved, all of the roles from the group are applied to a user
    // existing roles that a user already had are not overwritten by the role group assignment
    // We can differentiate roles assigned directly and from a role group
}
