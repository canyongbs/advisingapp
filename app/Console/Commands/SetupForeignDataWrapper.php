<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DataTransferObjects\ForeignDataWrapperData;

class SetupForeignDataWrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-foreign-data-wrapper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup foreign data wrapper for SIS database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        resolve(\App\Actions\Setup\SetupForeignDataWrapper::class)->handle(
            new ForeignDataWrapperData(
                connection: 'pgsql',
                localServerName: 'sis_bridge',
                externalHost: 'redshift',
                externalPort: '5433',
                externalUser: 'sail',
                externalPassword: 'password',
                externalDatabase: 'sis',
                tables: [
                    'students',
                    'programs',
                    'enrollment',
                ],
            )
        );
    }
}
