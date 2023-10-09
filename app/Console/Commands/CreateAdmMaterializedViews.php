<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\Setup\SetupAdmMaterializedViews;

class CreateAdmMaterializedViews extends Command
{
    protected $signature = 'app:create-adm-materialized-views';

    protected $description = 'Creates the materialized views for the ADM tables';

    public function handle(): void
    {
        if (! config('database.adm_materialized_views_enabled')) {
            $this->warn('ADM materialized views are not enabled, skipping...');

            return;
        }

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'students',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'programs',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'enrollments',
            indexColumn: 'sisid',
        );

        resolve(SetupAdmMaterializedViews::class)->handle(
            connection: 'pgsql',
            remoteTable: 'performance',
            indexColumn: 'sisid',
        );
    }
}
