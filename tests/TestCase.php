<?php

namespace Tests;

use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected $connectionsToTransact = [
        'mysql',
        'sis',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->artisan('migrate:fresh');
        $this->artisan('migrate:fresh', ['--database' => 'sis', '--path' => 'database/migrations/sis']);
    }
}
