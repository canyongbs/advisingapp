<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->artisan('migrate:fresh');
        $this->artisan('migrate:fresh', ['--database' => 'sis', '--path' => 'database/migrations/sis']);

        $this->artisan('app:setup-foreign-data-wrapper');

        DB::connection('pgsql')->beginTransaction();
        DB::connection('sis')->beginTransaction();
    }

    public function tearDown(): void
    {
        DB::connection('pgsql')->rollBack();
        DB::connection('sis')->rollBack();
        parent::tearDown();
    }
}
