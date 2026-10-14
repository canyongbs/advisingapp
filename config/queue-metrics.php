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

use Cbox\LaravelQueueMetrics\Actions\CalculateBaselinesAction;
use Cbox\LaravelQueueMetrics\Actions\CalculateJobMetricsAction;
use Cbox\LaravelQueueMetrics\Actions\RecordJobCompletionAction;
use Cbox\LaravelQueueMetrics\Actions\RecordJobFailureAction;
use Cbox\LaravelQueueMetrics\Actions\RecordJobStartAction;
use Cbox\LaravelQueueMetrics\Actions\RecordQueueDepthHistoryAction;
use Cbox\LaravelQueueMetrics\Actions\RecordThroughputHistoryAction;
use Cbox\LaravelQueueMetrics\Actions\RecordWorkerHeartbeatAction;
use Cbox\LaravelQueueMetrics\Actions\TransitionWorkerStateAction;
use Cbox\LaravelQueueMetrics\Http\Middleware\Authorize;
use Cbox\LaravelQueueMetrics\Repositories\Contracts\BaselineRepository;
use Cbox\LaravelQueueMetrics\Repositories\Contracts\JobMetricsRepository;
use Cbox\LaravelQueueMetrics\Repositories\Contracts\QueueMetricsRepository;
use Cbox\LaravelQueueMetrics\Repositories\Contracts\WorkerHeartbeatRepository;
use Cbox\LaravelQueueMetrics\Repositories\Contracts\WorkerRepository;

// Published from cboxdk/laravel-queue-metrics. Numeric env() reads are cast because ECS injects every environment
// variable as a string and the package hands these straight to int/float-typed sinks.
return [
    /*
    |--------------------------------------------------------------------------
    | 👉 BASIC
    |--------------------------------------------------------------------------
    */

    'enabled' => env('QUEUE_METRICS_ENABLED', true),

    'persistence' => [
        'enabled' => env('QUEUE_METRICS_PERSISTENCE', true),
    ],

    'storage' => [
        'driver' => env('QUEUE_METRICS_STORAGE', 'redis'),
        'connection' => env('QUEUE_METRICS_CONNECTION', 'default'),
        'prefix' => 'queue_metrics',

        // Maximum retained samples per metric key.
        // Recommended: 1000 for Redis, 500 for database driver.
        'max_samples_per_key' => (int) env('QUEUE_METRICS_MAX_SAMPLES', 1000),
        'cleanup_chunk_size' => 1000,

        'ttl' => [
            'raw' => 3600,
            'aggregated' => 604800,
            'baseline' => 2592000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🔒 SECURITY
    |--------------------------------------------------------------------------
    */

    'middleware' => ['api', Authorize::class],

    'allowed_ips' => is_string($allowedIps = env('QUEUE_METRICS_ALLOWED_IPS')) && $allowedIps !== '' ? explode(',', $allowedIps) : null,

    /*
    |--------------------------------------------------------------------------
    | 📊 PROMETHEUS
    |--------------------------------------------------------------------------
    */

    'prometheus' => [
        // Off: we publish scaling metrics to CloudWatch, and spatie/laravel-prometheus is not registered.
        'enabled' => false,
        'namespace' => env('QUEUE_METRICS_PROMETHEUS_NAMESPACE', 'laravel_queue'),
        'cache_ttl' => (int) env('QUEUE_METRICS_PROMETHEUS_CACHE_TTL', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | 📡 TELEMETRY (cboxdk/laravel-telemetry)
    |--------------------------------------------------------------------------
    |
    | When cboxdk/laravel-telemetry is installed, queue metrics automatically
    | publishes observable gauges (queue depth, worker state, baselines) and
    | pushes domain events (health changes, depth threshold breaches,
    | debounced jobs) to it. Batteries included: on by default, no-op when
    | the package is not installed.
    |
    | Tip: run `php artisan queue-metrics:doctor` — it warns when both this
    | integration and the built-in Prometheus exporter are active, which
    | would expose the same metrics twice.
    |
    */

    'telemetry' => [
        'enabled' => env('QUEUE_METRICS_TELEMETRY_ENABLED', true),

        // Snapshot cache in seconds, so concurrent scrapes don't all scan
        // the metrics store at once. 0 disables caching.
        'cache_ttl' => (int) env('QUEUE_METRICS_TELEMETRY_CACHE_TTL', 10),

        // Observable gauge groups, evaluated at scrape time.
        'gauges' => [
            'queues' => true,
            'workers' => true,
            'baselines' => true,
        ],

        // Push counters/gauges/OTLP events from package domain events
        // (health score changes, depth threshold breaches, debounces,
        // worker efficiency, baseline recalculations).
        'events' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | ⚙️ ADVANCED
    |--------------------------------------------------------------------------
    */

    'worker_heartbeat' => [
        'stale_threshold' => (int) env('QUEUE_METRICS_STALE_THRESHOLD', 60),
    ],

    'baseline' => [
        'sliding_window_days' => (int) env('QUEUE_METRICS_BASELINE_WINDOW_DAYS', 7),
        'decay_factor' => (float) env('QUEUE_METRICS_BASELINE_DECAY_FACTOR', 0.1),
        'target_sample_size' => (int) env('QUEUE_METRICS_BASELINE_TARGET_SAMPLES', 200),

        'intervals' => [
            'no_baseline' => 1,
            'low_confidence' => 5,
            'medium_confidence' => 10,
            'high_confidence' => 30,
            'very_high_confidence' => 60,
        ],

        'deviation' => [
            'enabled' => env('QUEUE_METRICS_BASELINE_DEVIATION_ENABLED', true),
            'threshold' => (float) env('QUEUE_METRICS_BASELINE_DEVIATION_THRESHOLD', 2.0),
            'trigger_interval' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 🛠️ EXTENSIBILITY
    |--------------------------------------------------------------------------
    */

    'repositories' => [
        JobMetricsRepository::class => null,
        QueueMetricsRepository::class => null,
        WorkerRepository::class => null,
        BaselineRepository::class => null,
        WorkerHeartbeatRepository::class => null,
    ],

    'actions' => [
        'record_job_start' => RecordJobStartAction::class,
        'record_job_completion' => RecordJobCompletionAction::class,
        'record_job_failure' => RecordJobFailureAction::class,
        'calculate_job_metrics' => CalculateJobMetricsAction::class,
        'record_worker_heartbeat' => RecordWorkerHeartbeatAction::class,
        'transition_worker_state' => TransitionWorkerStateAction::class,
        'record_queue_depth_history' => RecordQueueDepthHistoryAction::class,
        'record_throughput_history' => RecordThroughputHistoryAction::class,
        'calculate_baselines' => CalculateBaselinesAction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | ⏰ SCHEDULING
    |--------------------------------------------------------------------------
    |
    | The package can automatically schedule necessary maintenance and recording
    | tasks. You can disable these if you prefer to schedule them manually
    | in your application's console kernel.
    |
    */

    'scheduling' => [
        // Off: the package schedules without onOneServer(), so app/Console/Kernel.php schedules these instead.
        'enabled' => false,
        'tasks' => [
            'cleanup_stale_workers' => true,
            'calculate_baselines' => true,
            'calculate_queue_metrics' => true,
            'record_trends' => true,
        ],
    ],
];
