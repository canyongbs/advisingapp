<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the app url', function () {
    $before = config()->get('app.url');

    $tenant = Tenant::first()->makeCurrent();

    $after = config()->get('app.url');

    $scheme = parse_url($before)['scheme'];

    assertEquals($after, "{$scheme}://{$tenant->domain}");

    Tenant::forgetCurrent();

    $after = config()->get('app.url');

    assertEquals($before, $after);
});
