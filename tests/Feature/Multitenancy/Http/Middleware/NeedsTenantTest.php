<?php

use App\Models\Tenant;
use Illuminate\Http\Request;

use function PHPUnit\Framework\assertTrue;

use Symfony\Component\HttpFoundation\Response;
use App\Multitenancy\Http\Middleware\NeedsTenant;

beforeEach(function () {
    Tenant::forgetCurrent();

    Route::get('/needs-tenant-test-route')->middleware(NeedsTenant::class);
});

it('redirects without a tenant', function () {
    $response = (new NeedsTenant())->handle(Request::create('/needs-tenant-test-route'), fn () => new Response());

    assertTrue($response->isRedirect(config('app.landlord_url')));
});

it('continues with a tenant', function () {
    Tenant::first()->makeCurrent();

    $response = (new NeedsTenant())->handle(Request::create('/needs-tenant-test-route'), fn () => new Response());

    assertTrue($response->isOk());
});
