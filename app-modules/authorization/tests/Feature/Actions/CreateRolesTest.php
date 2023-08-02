<?php

use Assist\Authorization\Actions\CreateRoles;

it('will create the roles defined in our config', function () {
    $this->markTestSkipped();

    resolve(CreateRoles::class)->handle();

    // TODO Check for roles that should be created from various modules
});
