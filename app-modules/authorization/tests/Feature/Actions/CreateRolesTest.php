<?php

namespace Assist\Authorization\Tests\Feature\Actions;

use Tests\TestCase;
use Assist\Authorization\Actions\CreateRoles;
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
