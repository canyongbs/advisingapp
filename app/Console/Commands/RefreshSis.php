<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;

class RefreshSis extends Command
{
    use TenantAware;

    protected $signature = 'sis:refresh {--tenant=*}';

    protected $description = 'Refreshes the local SIS database.';

    public function handle(): void
    {
        if (! app()->environment('local')) {
            $this->error('This command can only be run in the local environment.');

            return;
        }

        $this->line('Refreshing SIS database...');

        $tenant = Tenant::current()->id;

        Artisan::call("sis:clear --tenant={$tenant}");

        $this->info('SIS database refreshed');

        $this->line('Loading pre-seeded SIS data...');

        $password = config('database.connections.sis.password');
        $host = config('database.connections.sis.host');
        $port = config('database.connections.sis.port');
        $database = config('database.connections.sis.database');
        $username = config('database.connections.sis.username');

        $result = Process::run("gunzip < ./resources/sql/advising-app-adm-data.gz | PGPASSWORD={$password} psql -h {$host} -p {$port} -U {$username} -d {$database} -q")
            ->throw();

        if ($result->failed()) {
            $this->error($result->output());

            return;
        }

        if (! empty($result->output())) {
            $this->info($result->output());
        }

        $this->info('Pre-seeded SIS data loaded');
    }
}
