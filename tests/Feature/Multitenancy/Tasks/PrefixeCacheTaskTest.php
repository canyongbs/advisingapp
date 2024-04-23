<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertStringContainsString;
use function PHPUnit\Framework\assertStringNotContainsString;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the cache prefix', function () {
    $before = config()->get('cache.prefix');
    assertStringNotContainsString('tenant_id_', $before);

    Tenant::first()->makeCurrent();

    $after = config()->get('cache.prefix');

    assertNotEquals($before, $after);
    assertStringContainsString('tenant_id_', $after);

    Tenant::forgetCurrent();

    $after = config()->get('cache.prefix');

    assertEquals($before, $after);
    assertStringNotContainsString('tenant_id_', $before);
});

it('forgets the cache', function () {
    $before = cache()->store(config()->get('cache.default'))->getStore();

    Tenant::first()->makeCurrent();

    $after = cache()->store(config()->get('cache.default'))->getStore();

    assertNotSame($before, $after);

    Tenant::forgetCurrent();

    $after = config()->get('cache.prefix');

    assertNotSame($before, $after);
});
