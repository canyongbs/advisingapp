<?php

namespace Assist\Authorization\Tests\Feature\Actions\RolesAndPermissions;

use Tests\TestCase;
use App\Actions\RolesAndPermissions\CreateRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateRolesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_will_create_the_roles_defined_in_our_config(): void
    {
        $this->markTestSkipped();

        resolve(CreateRoles::class)->handle();

        // TODO Check for roles that should be created from various modules
    }
}
