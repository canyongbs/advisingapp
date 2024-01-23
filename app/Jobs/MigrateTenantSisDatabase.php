<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Middleware\SkipIfNotLocal;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use App\Multitenancy\DataTransferObjects\TenantConfig;

class MigrateTenantSisDatabase implements ShouldQueue, NotTenantAware
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Tenant $tenant) {}

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled(),
            new SkipIfNotLocal(),
        ];
    }

    public function handle(): void
    {
        $this->tenant->execute(function () {
            Artisan::call(
                command: 'migrate --database=sis --path=app-modules/student-data-model/database/migrations/sis'
            );

            /** @var TenantConfig $config */
            $config = $this->tenant->config;

            $password = $config->sisDatabase->password;
            $host = $config->sisDatabase->host;
            $port = $config->sisDatabase->port;
            $database = $config->sisDatabase->database;
            $username = $config->sisDatabase->username;

            Process::run("gunzip < ./resources/sql/advising-app-adm-data.gz | PGPASSWORD={$password} psql -h {$host} -p {$port} -U {$username} -d {$database} -q")
                ->throw();
        });
    }
}
