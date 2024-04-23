<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the app name', function () {
    $before = config()->get('app.name');

    Tenant::first()->makeCurrent();

    $after = config()->get('app.name');

    assertNotEquals($before, $after);

    Tenant::forgetCurrent();

    $after = config()->get('app.name');

    assertEquals($before, $after);
});
