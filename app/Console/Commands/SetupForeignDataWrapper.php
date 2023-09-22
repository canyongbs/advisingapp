<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DataTransferObjects\ForeignDataWrapperData;

class SetupForeignDataWrapper extends Command
{
    protected $signature = 'app:setup-foreign-data-wrapper';

    protected $description = 'Setup foreign data wrapper for SIS database';

    public function handle(): void
    {
        resolve(\App\Actions\Setup\SetupForeignDataWrapper::class)->handle(
            new ForeignDataWrapperData(
                connection: config('database.fdw.connection'),
                localServerName: config('database.fdw.server_name'),
                externalHost: config('database.fdw.external_host'),
                externalPort: config('database.fdw.external_port'),
                externalUser: config('database.fdw.external_user'),
                externalPassword: config('database.fdw.external_password'),
                externalDatabase: config('database.fdw.external_database'),
                tables: [
                    'students',
                    'programs',
                    'enrollments',
                    'performance',
                ],
            )
        );
    }
}
