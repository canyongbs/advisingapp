<?php

namespace Assist\LaravelAuditing\Tests\Functional;

use Assist\LaravelAuditing\Tests\AuditingTestCase;

class CommandTest extends AuditingTestCase
{
    /**
     * @test
     */
    public function itWillGenerateTheAuditDriver()
    {
        $driverFilePath = sprintf(
            '%s/AuditDrivers/TestDriver.php',
            $this->app->path()
        );

        $className = '\Illuminate\Testing\PendingCommand';

        if (class_exists('Illuminate\Foundation\Testing\PendingCommand')) {
            $className = '\Illuminate\Foundation\Testing\PendingCommand';
        }

        $this->assertInstanceOf(
            $className,
            $this->artisan(
                'auditing:audit-driver',
                [
                    'name' => 'TestDriver',
                ]
            )
        );

        $this->assertFileExists($driverFilePath);

        $this->assertTrue(unlink($driverFilePath));
    }
}
