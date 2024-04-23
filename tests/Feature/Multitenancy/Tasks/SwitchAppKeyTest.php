<?php

use App\Models\Tenant;
use Illuminate\Support\Arr;
use App\Multitenancy\Tasks\SwitchAppKey;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the app key', function () {
    $before = config()->get('app.key');

    Tenant::first()->makeCurrent();

    $after = config()->get('app.key');

    assertNotEquals($before, $after);

    Tenant::forgetCurrent();

    $after = config()->get('app.key');

    assertEquals($before, $after);
})->skip(
    fn () => Arr::has(config('multitenancy.switch_tenant_tasks'), SwitchAppKey::class) === false,
    'SwitchAppKey is not registered as a switch Tenant task'
);
