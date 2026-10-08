<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Providers;

use App\Features\QueueMonitoringFeature;
use App\Listeners\RecordWorkerCountSample;
use App\Models\Authenticatable;
use App\Models\Tenant;
use App\Overrides\Laravel\PostgresBatchRepository;
use App\Overrides\QueueAutoscale\ExitReportingWorkerSpawner;
use Aws\CloudWatch\CloudWatchClient;
use Cbox\LaravelQueueAutoscale\Configuration\AutoscaleConfiguration;
use Cbox\LaravelQueueAutoscale\Events\ClusterSummaryPublished;
use Cbox\LaravelQueueAutoscale\Events\ScalingDecisionMade;
use Cbox\LaravelQueueAutoscale\Workers\WorkerSpawner;
use Cbox\LaravelQueueMetrics\LaravelQueueMetrics;
use Cbox\LaravelQueueMonitor\LaravelQueueMonitor;
use Cbox\LaravelQueueMonitor\Models\JobMonitor;
use Closure;
use Illuminate\Bus\BatchFactory;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class QueueObservabilityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The ECS task role is the intended credential source; an explicit key and secret are the fallback.
        $this->app->bind(CloudWatchClient::class, function (): CloudWatchClient {
            $config = [
                'version' => 'latest',
                'region' => Config::string('services.cloudwatch.region'),
            ];

            $key = Config::string('services.cloudwatch.key');
            $secret = Config::string('services.cloudwatch.secret');

            if (filled($key) && filled($secret)) {
                $config['credentials'] = ['key' => $key, 'secret' => $secret];
            }

            return new CloudWatchClient($config);
        });

        // The autoscale package registers after this provider and would overwrite a plain binding, so extend.
        $this->app->extend(WorkerSpawner::class, fn (WorkerSpawner $spawner, Application $app): WorkerSpawner => $app->make(ExitReportingWorkerSpawner::class));

        // BusServiceProvider is deferred and would overwrite a plain binding, so extend at resolve time. That also
        // follows SwitchTenantDatabasesTask, which forgets the instance when it switches the batching connection.
        $this->app->extend(DatabaseBatchRepository::class, function (DatabaseBatchRepository $repository, Application $app): DatabaseBatchRepository {
            $database = Config::get('queue.batching.database');

            assert(is_string($database) || $database === null);

            $connection = $app->make(DatabaseManager::class)->connection($database);

            if ($connection->getDriverName() !== 'pgsql') {
                return $repository;
            }

            return new PostgresBatchRepository(
                $app->make(BatchFactory::class),
                $connection,
                Config::string('queue.batching.table', 'job_batches'),
            );
        });
    }

    public function boot(): void
    {
        // Must boot before the queue monitor's provider, which only registers its listeners while it is enabled.
        if (! rescue(fn (): bool => QueueMonitoringFeature::active(), false, report: false)) {
            config(['queue-monitor.enabled' => false]);
        }

        JobMonitor::creating(function (JobMonitor $jobMonitor): void {
            $jobMonitor->setAttribute('tenant_id', $jobMonitor->getAttribute('tenant_id') ?? Tenant::current()?->getKey());
        });

        // In cluster mode each host's ScalingDecisionMade only carries its local share of the workers.
        if (AutoscaleConfiguration::clusterEnabled()) {
            Event::listen(ClusterSummaryPublished::class, [RecordWorkerCountSample::class, 'recordClusterSummary']);
        } else {
            Event::listen(ScalingDecisionMade::class, [RecordWorkerCountSample::class, 'recordScalingDecision']);
        }

        LaravelQueueMonitor::auth($this->superAdmins());
        LaravelQueueMetrics::auth($this->superAdmins());
    }

    /**
     * @return Closure(Request): bool
     */
    private function superAdmins(): Closure
    {
        return function (Request $request): bool {
            $user = $request->user();

            return $user instanceof Authenticatable && $user->isSuperAdmin();
        };
    }
}
