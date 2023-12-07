<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\LaravelAuditing\Tests;

use Orchestra\Testbench\TestCase;
use Assist\LaravelAuditing\Resolvers\UrlResolver;
use Assist\LaravelAuditing\Resolvers\UserResolver;
use Assist\LaravelAuditing\AuditingServiceProvider;
use Assist\LaravelAuditing\Resolvers\IpAddressResolver;
use Assist\LaravelAuditing\Resolvers\UserAgentResolver;

class AuditingTestCase extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__ . '/database/factories');
    }

    /**
     * Locate the Illuminate testing class. It changed namespace with v7
     *
     * @see https://readouble.com/laravel/7.x/en/upgrade.html
     *
     * @return class-string<\Illuminate\Foundation\Testing\Assert|\Illuminate\Testing\Assert>
     */
    public static function Assert(): string
    {
        if (class_exists('Illuminate\Foundation\Testing\Assert')) {
            return '\Illuminate\Foundation\Testing\Assert';
        }

        return '\Illuminate\Testing\Assert';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        // Database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Audit
        $app['config']->set('audit.drivers.database.table', 'audit_testing');
        $app['config']->set('audit.drivers.database.connection', 'testing');
        $app['config']->set('audit.user.morph_prefix', 'prefix');
        $app['config']->set('audit.user.resolver', UserResolver::class);
        $app['config']->set('audit.user.guards', [
            'web',
            'api',
        ]);
        $app['config']->set('auth.guards.api', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        $app['config']->set('audit.resolvers.url', UrlResolver::class);
        $app['config']->set('audit.resolvers.ip_address', IpAddressResolver::class);
        $app['config']->set('audit.resolvers.user_agent', UserAgentResolver::class);
        $app['config']->set('audit.console', true);
        $app['config']->set('audit.empty_values', true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            AuditingServiceProvider::class,
        ];
    }
}
