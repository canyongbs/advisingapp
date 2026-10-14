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

use Cbox\LaravelQueueMonitor\Actions\Analytics\CalculateJobStatisticsAction;
use Cbox\LaravelQueueMonitor\Actions\Analytics\CalculateQueueHealthAction;
use Cbox\LaravelQueueMonitor\Actions\Analytics\CalculateServerStatisticsAction;
use Cbox\LaravelQueueMonitor\Actions\Batch\BatchDeleteAction;
use Cbox\LaravelQueueMonitor\Actions\Batch\BatchReplayAction;
use Cbox\LaravelQueueMonitor\Actions\Core\CancelJobAction;
use Cbox\LaravelQueueMonitor\Actions\Core\PruneJobsAction;
use Cbox\LaravelQueueMonitor\Actions\Core\RecordJobCompletedAction;
use Cbox\LaravelQueueMonitor\Actions\Core\RecordJobFailedAction;
use Cbox\LaravelQueueMonitor\Actions\Core\RecordJobQueuedAction;
use Cbox\LaravelQueueMonitor\Actions\Core\RecordJobStartedAction;
use Cbox\LaravelQueueMonitor\Actions\Core\RecordJobTimeoutAction;
use Cbox\LaravelQueueMonitor\Actions\Replay\ReplayJobAction;
use Cbox\LaravelQueueMonitor\Repositories\Contracts\JobMonitorRepositoryContract;
use Cbox\LaravelQueueMonitor\Repositories\Contracts\StatisticsRepositoryContract;
use Cbox\LaravelQueueMonitor\Repositories\Contracts\TagRepositoryContract;
use Cbox\LaravelQueueMonitor\Repositories\Eloquent\EloquentJobMonitorRepository;
use Cbox\LaravelQueueMonitor\Repositories\Eloquent\EloquentStatisticsRepository;
use Cbox\LaravelQueueMonitor\Repositories\Eloquent\EloquentTagRepository;
use Cbox\LaravelQueueMonitor\Services\AlertingService;
use Cbox\LaravelQueueMonitor\Services\Contracts\AlertingServiceContract;
use Cbox\LaravelQueueMonitor\Services\Contracts\DashboardCacheServiceContract;
use Cbox\LaravelQueueMonitor\Services\Contracts\ExportServiceContract;
use Cbox\LaravelQueueMonitor\Services\Contracts\HealthCheckServiceContract;
use Cbox\LaravelQueueMonitor\Services\Contracts\InfrastructureServiceContract;
use Cbox\LaravelQueueMonitor\Services\Contracts\WorkerContextServiceContract;
use Cbox\LaravelQueueMonitor\Services\DashboardCacheService;
use Cbox\LaravelQueueMonitor\Services\ExportService;
use Cbox\LaravelQueueMonitor\Services\HealthCheckService;
use Cbox\LaravelQueueMonitor\Services\InfrastructureService;
use Cbox\LaravelQueueMonitor\Services\WorkerContextService;

// Published from cboxdk/laravel-queue-monitor. Numeric env() reads are cast because ECS injects every environment
// variable as a string and the package hands these straight to int/float-typed sinks.
return [
    /*
    |--------------------------------------------------------------------------
    | Queue Monitor Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the queue monitor package is active.
    | Set to false to completely disable job monitoring without uninstalling.
    | Also forced off until App\Features\QueueMonitoringFeature is active (App\Providers\QueueObservabilityServiceProvider).
    |
    */
    // TODO: Cleanup Task (queue-monitoring): remove the QueueMonitoringFeature line from the comment above.
    'enabled' => env('QUEUE_MONITOR_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Run Package Migrations
    |--------------------------------------------------------------------------
    |
    | The tables are created by our own migrations in database/landlord instead.
    |
    */
    'enable_migrations' => false,

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Jobs are recorded wherever they are dispatched or processed, often in a tenant context, so the connection is
    | pinned to the landlord database. The prefix must match the table names in database/landlord.
    |
    */
    'database' => [
        'connection' => 'landlord',
        'table_prefix' => 'queue_monitor_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payload Storage
    |--------------------------------------------------------------------------
    |
    | Configure how job payloads are stored for replay functionality.
    |
    */
    'storage' => [
        // Store complete job payload for replay capability.
        // Defaults to enabled in local only; set QUEUE_MONITOR_STORE_PAYLOAD explicitly to override.
        'store_payload' => env('QUEUE_MONITOR_STORE_PAYLOAD', env('APP_ENV') === 'local'),

        // Maximum payload size in bytes (default: 64KB)
        'payload_max_size' => (int) env('QUEUE_MONITOR_PAYLOAD_MAX_SIZE', 65535),

        // Defer tag storage to queue for better performance
        // When true, tags are stored asynchronously after job completion
        'deferred_tags' => env('QUEUE_MONITOR_DEFERRED_TAGS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Retention
    |--------------------------------------------------------------------------
    |
    | Configure automatic cleanup of old job records. Both time-based and
    | row-count limits are enforced — whichever triggers first wins.
    | This prevents unbounded table growth in high-throughput environments.
    |
    */
    'retention' => [
        // Number of days to retain job records (applies to statuses below)
        'days' => 7,

        // Maximum number of rows to keep in the jobs table.
        // When exceeded, oldest prunable rows are deleted first.
        // Set to null to disable row-count pruning (time-based only).
        'max_rows' => (int) env('QUEUE_MONITOR_MAX_ROWS', 250_000),

        // All terminal statuses. Live `queued` rows are never pruned: an aged `queued` row is indistinguishable from a
        // job still waiting in a deep backlog.
        'prune_statuses' => ['completed', 'failed', 'timeout', 'cancelled', 'debounced'],

        // Minutes after which a job still in `processing` is treated as stuck and
        // marked `timeout` by the queue-monitor:resolve-stuck command. Null disables
        // automatic resolution (the default). Detection is start-age based with no
        // heartbeat, so set this comfortably above your longest legitimate job so a
        // slow-but-live job is never timed out. A blank or non-numeric value disables it rather than casting to 0,
        // which would time out every in-flight job.
        'resolve_stuck_after_minutes' => is_numeric($resolveStuckAfterMinutes = env('QUEUE_MONITOR_RESOLVE_STUCK_AFTER_MINUTES', 360))
            ? (int) $resolveStuckAfterMinutes
            : null,

        // Days to retain full JSON payloads in cluster_events.meta.
        // After this period, meta is nulled but typed columns preserved for trends.
        // Set to null to disable payload pruning (keep payloads for full retention period).
        'payload_days' => (int) env('QUEUE_MONITOR_PAYLOAD_DAYS', 2),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cluster leadership
    |--------------------------------------------------------------------------
    |
    | Taking the autoscaler's cluster lease discards worker placement, the
    | anti-flapping window and the fair-share ledger's position, because each
    | describes a cluster the new leader has not observed. One failover costs a
    | cycle; leadership that keeps moving means none of them ever completes.
    |
    | When this many leadership changes land inside the window, a
    | `leadership_unstable` cluster event is recorded — once per window, so an
    | unstable cluster does not bury its own timeline. The default window
    | matches the autoscaler's own anti-flapping window, which is the yardstick
    | every piece of discarded state is sized in.
    |
    */
    'cluster' => [
        'leadership_window_seconds' => (int) env('QUEUE_MONITOR_LEADERSHIP_WINDOW', 60),
        'leadership_change_threshold' => (int) env('QUEUE_MONITOR_LEADERSHIP_THRESHOLD', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Worker Detection
    |--------------------------------------------------------------------------
    |
    | Configure how workers and servers are detected and identified.
    |
    */
    'worker_detection' => [
        // Custom callable for determining server name
        // If null, uses gethostname()
        'server_name_callable' => null,

        // Enable Horizon detection
        'horizon_detection' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for statistics queries to improve performance. The landlord store keeps one cache shared
    | across tenant contexts.
    |
    */
    'cache' => [
        'enabled' => env('QUEUE_MONITOR_CACHE_ENABLED', true),
        'store' => 'landlord',
        'ttl' => (int) env('QUEUE_MONITOR_CACHE_TTL', 300), // seconds

        // Minimum seconds between statistics-cache version bumps. Job lifecycle
        // writes invalidate the cache; without this throttle a busy queue
        // invalidates faster than any entry can be reused. The first write
        // after a quiet period still invalidates immediately.
        'bust_throttle_seconds' => (int) env('QUEUE_MONITOR_CACHE_BUST_THROTTLE', 5),

        'prefix' => 'queue_monitor_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Checks
    |--------------------------------------------------------------------------
    |
    | Tune health thresholds for different queue volumes and environments.
    |
    */
    'health' => [
        'stuck_job_minutes' => (int) env('QUEUE_MONITOR_HEALTH_STUCK_JOB_MINUTES', 30),
        'error_rate_threshold' => (float) env('QUEUE_MONITOR_HEALTH_ERROR_RATE_THRESHOLD', 10.0),
        // Error rate (%) at which the alert escalates from warning to critical.
        'error_rate_critical_threshold' => (float) env('QUEUE_MONITOR_HEALTH_ERROR_RATE_CRITICAL_THRESHOLD', 20.0),
        'queued_jobs_threshold' => (int) env('QUEUE_MONITOR_HEALTH_QUEUED_JOBS_THRESHOLD', 1000),
        'processing_jobs_threshold' => (int) env('QUEUE_MONITOR_HEALTH_PROCESSING_JOBS_THRESHOLD', 100),
        'storage_max_mb' => (int) env('QUEUE_MONITOR_HEALTH_STORAGE_MAX_MB', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Time Window
    |--------------------------------------------------------------------------
    |
    | Limit dashboard statistics queries to recent data instead of scanning
    | the entire table. Prevents slow aggregation on high-throughput systems.
    | Value in hours. Set to null for all-time stats (not recommended).
    |
    */
    'metrics_window_hours' => (int) env('QUEUE_MONITOR_METRICS_WINDOW_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Batch Operations Configuration
    |--------------------------------------------------------------------------
    |
    | Configure batch operation behavior for bulk actions.
    |
    */
    'batch' => [
        'chunk_size' => (int) env('QUEUE_MONITOR_BATCH_CHUNK_SIZE', 100),
        'max_jobs' => (int) env('QUEUE_MONITOR_BATCH_MAX_JOBS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | REST API Configuration
    |--------------------------------------------------------------------------
    |
    | Off: its routes have no authorization of their own. The Filament Queue Monitoring page reads the data instead.
    |
    */
    'api' => [
        'enabled' => false,
        'prefix' => 'api/queue-monitor',
        'middleware' => ['api'],
        'rate_limit' => '60,1', // 60 requests per minute
        'default_limit' => (int) env('QUEUE_MONITOR_API_DEFAULT_LIMIT', 50),
        'max_limit' => (int) env('QUEUE_MONITOR_API_MAX_LIMIT', 1000),

        // Keys to mask in the payload response (e.g. password, token, secret)
        // Set to empty array to disable redaction
        'sensitive_keys' => ['password', 'token', 'secret', 'key', 'authorization', 'api_key', 'credit_card', 'cvv', 'ssn', 'private_key', 'command'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Keep API exports bounded so integrations cannot accidentally load the
    | entire job table into memory.
    |
    */
    'export' => [
        'default_limit' => (int) env('QUEUE_MONITOR_EXPORT_DEFAULT_LIMIT', 1000),
        'max_rows' => (int) env('QUEUE_MONITOR_EXPORT_MAX_ROWS', 5000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Web Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Off: its routes have no authorization of their own. The Filament Queue Monitoring page replaces it.
    |
    */
    'ui' => [
        'enabled' => false,
        'route_prefix' => 'queue-monitor',
        'middleware' => ['web'],
        'per_page' => 35,
        'refresh_interval' => 3000, // ms

        // Dashboard asset loading strategy:
        // - served: stream the bundled assets from a package route with a long
        //   immutable cache (default, zero setup, browser-cached across loads)
        // - inline: embed the precompiled assets in the dashboard HTML (no extra
        //   requests, but the ~1 MB chart library is re-sent on every load; use
        //   for a strict CSP that forbids external scripts)
        // - public: load files from public/vendor/queue-monitor after publishing queue-monitor-assets
        // - none: emit no package assets; use a published/custom view and your own app build
        'assets' => [
            'mode' => env('QUEUE_MONITOR_ASSET_MODE', 'served'),
            'url' => env('QUEUE_MONITOR_ASSET_URL'), // null defaults to asset('vendor/queue-monitor')
            'paths' => [
                'css' => 'queue-monitor.css',
                'echarts' => 'echarts.min.js',
                'alpine' => 'alpine.min.js',
            ],
        ],

        // Color thresholds for CPU and memory utilization in the job list.
        // Values are percentages. Below 'warning' = green, warning–critical = amber, above 'critical' = red.
        'cpu_thresholds' => [
            'warning' => 50,
            'critical' => 80,
        ],
        'memory_thresholds' => [
            'warning' => 60,
            'critical' => 80,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Bindings
    |--------------------------------------------------------------------------
    |
    | Map repository contracts to their concrete implementations.
    | Override these to provide custom repository implementations.
    |
    */
    'repositories' => [
        JobMonitorRepositoryContract::class => EloquentJobMonitorRepository::class,
        TagRepositoryContract::class => EloquentTagRepository::class,
        StatisticsRepositoryContract::class => EloquentStatisticsRepository::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Bindings
    |--------------------------------------------------------------------------
    |
    | Map service contracts to their concrete implementations.
    | Every domain service is resolved through its contract, so a host can
    | override or fake any of them by rebinding the contract here.
    |
    */
    'services' => [
        AlertingServiceContract::class => AlertingService::class,
        DashboardCacheServiceContract::class => DashboardCacheService::class,
        ExportServiceContract::class => ExportService::class,
        HealthCheckServiceContract::class => HealthCheckService::class,
        InfrastructureServiceContract::class => InfrastructureService::class,
        WorkerContextServiceContract::class => WorkerContextService::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Action Bindings
    |--------------------------------------------------------------------------
    |
    | Map action classes for dependency injection and extensibility.
    | Override these to provide custom action implementations.
    |
    */
    'actions' => [
        // Core Actions
        'record_job_queued' => RecordJobQueuedAction::class,
        'record_job_started' => RecordJobStartedAction::class,
        'record_job_completed' => RecordJobCompletedAction::class,
        'record_job_failed' => RecordJobFailedAction::class,
        'record_job_timeout' => RecordJobTimeoutAction::class,
        'cancel_job' => CancelJobAction::class,
        'prune_jobs' => PruneJobsAction::class,

        // Replay Actions
        'replay_job' => ReplayJobAction::class,

        // Batch Actions
        'batch_replay' => BatchReplayAction::class,
        'batch_delete' => BatchDeleteAction::class,

        // Analytics Actions
        'calculate_job_statistics' => CalculateJobStatisticsAction::class,
        'calculate_server_statistics' => CalculateServerStatisticsAction::class,
        'calculate_queue_health' => CalculateQueueHealthAction::class,
    ],
];
