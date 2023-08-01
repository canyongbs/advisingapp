<?php

use Assist\Authorization\Actions\CreateRoles;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('will create the roles defined in our config', function () {
    $this->markTestSkipped();

    resolve(CreateRoles::class)->handle();

    // TODO Check for roles that should be created from various modules
});
