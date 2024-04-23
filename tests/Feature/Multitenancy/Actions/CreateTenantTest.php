<?php

use App\Models\Tenant;

use function Pest\Laravel\artisan;

use App\Console\Commands\CreateTenant;
use App\Multitenancy\Events\NewTenantSetupComplete;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('creates a tenant', function () {
    Event::fake(NewTenantSetupComplete::class);

    $name = str(fake()->words(asText: true));
    $domain = $name->slug()->toString() . '.' . config('app.landlord_host');

    DB::commit();

    artisan(CreateTenant::class, [
        'name' => $name->toString(),
        'domain' => $domain,
        '--run-queue' => true,
        '--seed' => true,
        '--admin' => true,
    ])->assertSuccessful();

    Event::assertDispatched(
        NewTenantSetupComplete::class,
        fn (NewTenantSetupComplete $event) => $event->tenant->name === $name->toString()
    );
});
