<?php

use App\Models\Tenant;

use function Pest\Laravel\get;
use function Pest\Laravel\artisan;

use Illuminate\Support\Facades\DB;
use App\Console\Commands\CreateTenant;

use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertNotSame;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('has a landlord', function () {
    get('/')->assertRedirect(config('app.landlord_url'));
});

it('switches tenants', function () {
    Tenant::forgetCurrent();

    $name = str(fake()->words(asText: true));
    $domain = $name->slug()->toString() . '.' . config('app.landlord_host');

    DB::commit();

    artisan(CreateTenant::class, [
        'name' => $name->toString(),
        'domain' => $domain,
        '--run-queue' => true,
        '--seed' => true,
        '--admin' => true,
    ]);

    $first = Tenant::first()->makeCurrent();

    assertSame(Tenant::current()->getKey(), $first->getKey());

    $second = Tenant::latest()->first()->makeCurrent();

    assertSame(Tenant::current()->getKey(), $second->getKey());

    assertNotSame($first->getKey(), $second->getKey());
});
