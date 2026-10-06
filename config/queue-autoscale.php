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

use App\Support\QueueAutoscale\LandlordFailureWindowStore;
use Cbox\LaravelQueueAutoscale\Configuration\Profiles\BalancedProfile;
use Cbox\LaravelQueueAutoscale\Fuse\ConfigurableFailureClassifier;
use Cbox\LaravelQueueAutoscale\Pickup\RedisPickupTimeStore;
use Cbox\LaravelQueueAutoscale\Pickup\SortBasedPercentileCalculator;
use Cbox\LaravelQueueAutoscale\Policies\BreachNotificationPolicy;
use Cbox\LaravelQueueAutoscale\Policies\ConservativeScaleDownPolicy;
use Cbox\LaravelQueueAutoscale\Scaling\Strategies\HybridStrategy;

// Published from cboxdk/laravel-queue-autoscale. Numeric env() reads are cast because ECS injects every environment
// variable as a string and the package hands these straight to int/float-typed sinks.
return [
    'enabled' => env('QUEUE_AUTOSCALE_ENABLED', true),
    'manager_id' => env('QUEUE_AUTOSCALE_MANAGER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Default Profile (per-queue settings)
    |--------------------------------------------------------------------------
    |
    | Provide a ProfileContract class OR a literal array matching the shape
    | returned by BalancedProfile::resolve(). See docs/advanced-usage/upgrade-guide-v3.md
    | for migration details from v2.
    |
    */
    'sla_defaults' => BalancedProfile::class,

    /*
    |--------------------------------------------------------------------------
    | Per-queue overrides
    |--------------------------------------------------------------------------
    |
    | Each value can be a ProfileContract class OR an array of partial overrides
    | that merges with sla_defaults.
    |
    | Optional 'resources' key declares per-queue CPU/memory estimates for
    | capacity calculations. These override the global limits when measured
    | data is not yet available (cold start). Once the autoscaler has enough
    | measured samples, measured values take precedence automatically.
    |
    |   'slow' => [
    |       'resources' => [
    |           'cpu_cores'  => 0.5,    // CPU cores per worker (default: limits.worker_cpu_core_estimate)
    |           'memory_mb'  => 2048,   // Memory MB per worker (default: limits.worker_memory_mb_estimate)
    |       ],
    |   ],
    |
    | A worker floor applies only to a queue named here.
    |
    | Queues are DISCOVERED from metrics, not registered, so an application
    | that mints a queue name per tenant presents thousands of them. A queue
    | matching no entry above — neither an exact name nor a glob — is given
    | 'workers.min' => 0, because a floor multiplied by a set nobody bounded is
    | unbounded process creation. Two exemptions: a non-scalable profile, where
    | scalable=false requires min == max by construction, and a defaults block
    | whose own min exceeds its max, which must stay invalid rather than be
    | silently repaired. Such a
    | queue still gets every other default: SLA target, max, forecast, spawn
    | compensation and fuse. It scales from zero on demand instead of holding
    | an idle worker forever.
    |
    | To floor every discovered queue anyway, say so explicitly:
    |
    |   '*' => ['workers' => ['min' => 1]],
    |
    | Doing that also trips the doctor's warning to set
    | limits.max_total_workers, which is the bound that makes it safe.
    |
    | One consequence worth knowing: the engine clamps a target to measured
    | CPU/memory BEFORE applying any floor. A named queue's floor overrides
    | that clamp; an unnamed queue now has no floor to override it with. So on
    | a host already at capacity, a discovered queue with a backlog gets zero
    | workers where it previously got one. That is the correct answer for a
    | full host — the fix for a full host is another host, which the cluster
    | scale signal already recommends — but name the queue if you want it
    | served regardless.
    |
    */
    'queues' => [
        // Named explicitly so a freshly deployed worker manages every queue immediately, rather than waiting for
        // metrics discovery to see it.

        env('SQS_QUEUE', 'default') => [
            'connection' => env('QUEUE_CONNECTION', 'sqs'),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_DEFAULT_SLA', 30)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_DEFAULT_MIN_WORKERS', 1), 'max' => (int) env('QUEUE_AUTOSCALE_DEFAULT_MAX_WORKERS', 500)],
        ],

        env('LANDLORD_SQS_QUEUE', 'landlord') => [
            'connection' => env('QUEUE_CONNECTION', 'sqs'),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_LANDLORD_SLA', 60)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_LANDLORD_MIN_WORKERS', 1), 'max' => (int) env('QUEUE_AUTOSCALE_LANDLORD_MAX_WORKERS', 150)],
        ],

        env('OUTBOUND_COMMUNICATION_QUEUE', 'outbound-communication') => [
            'connection' => env('QUEUE_CONNECTION', 'sqs'),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_OUTBOUND_COMMUNICATION_SLA', 30)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_OUTBOUND_COMMUNICATION_MIN_WORKERS', 1), 'max' => (int) env('QUEUE_AUTOSCALE_OUTBOUND_COMMUNICATION_MAX_WORKERS', 300)],
        ],

        env('AUDIT_QUEUE_QUEUE', 'audit') => [
            'connection' => env('AUDIT_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sqs')),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_AUDIT_SLA', 150)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_AUDIT_MIN_WORKERS', 1), 'max' => (int) env('QUEUE_AUTOSCALE_AUDIT_MAX_WORKERS', 150)],
        ],

        env('MEETING_CENTER_QUEUE', 'meeting-center') => [
            'connection' => env('QUEUE_CONNECTION', 'sqs'),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_MEETING_CENTER_SLA', 150)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_MEETING_CENTER_MIN_WORKERS', 0), 'max' => (int) env('QUEUE_AUTOSCALE_MEETING_CENTER_MAX_WORKERS', 150)],
        ],

        env('IMPORT_EXPORT_QUEUE', 'import-export') => [
            'connection' => env('QUEUE_CONNECTION', 'sqs'),
            'sla' => ['target_seconds' => (int) env('QUEUE_AUTOSCALE_IMPORT_EXPORT_SLA', 300)],
            'workers' => ['min' => (int) env('QUEUE_AUTOSCALE_IMPORT_EXPORT_MIN_WORKERS', 0), 'max' => (int) env('QUEUE_AUTOSCALE_IMPORT_EXPORT_MAX_WORKERS', 150)],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded queues
    |--------------------------------------------------------------------------
    |
    | Queue name patterns (fnmatch globs) that this package will never manage.
    | Use this for queues supervised elsewhere (e.g. by Horizon), throwaway
    | queues, or queues you want to run manually with a fixed worker count.
    |
    | Examples: 'legacy-*', 'test-?', 'horizon-managed'
    |
    */
    'excluded' => [
        // 'legacy-*',
        // 'horizon-managed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Worker groups (multi-queue workers with strict priority)
    |--------------------------------------------------------------------------
    |
    | A group runs one set of workers that polls multiple queues in priority
    | order (first listed = highest priority). Use groups when several queues
    | share a workload budget and you want idle workers in one queue to absorb
    | bursts on another without paying spawn latency.
    |
    | A queue name may appear EITHER under 'queues' OR inside a group's
    | 'queues' list, never both. Start-up validation will fail if it does.
    |
    | Supported 'mode' values: 'priority' (the only mode in v2).
    |
    | Example:
    |     'notifications' => [
    |         'queues' => ['email', 'sms', 'push'],
    |         'profile' => BalancedProfile::class,
    |         'connection' => 'redis',
    |         'mode' => 'priority',
    |     ],
    |
    */
    'groups' => [
        // 'notifications' => [
        //     'queues' => ['email', 'sms', 'push'],
        //     'profile' => BalancedProfile::class,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pickup time storage (global)
    |--------------------------------------------------------------------------
    |
    | Controls the optional shared store used for p95 pickup-time forecasting.
    |
    | Supported values:
    | - 'auto'  => Redis in cluster mode, null/no-op in single-host mode
    | - 'redis' => force RedisPickupTimeStore
    | - 'null'  => disable shared pickup-time persistence
    | - FQCN    => custom PickupTimeStoreContract implementation
    |
    */
    'pickup_time' => [
        'store' => env('QUEUE_AUTOSCALE_PICKUP_TIME_STORE', 'auto'),
        'percentile_calculator' => SortBasedPercentileCalculator::class,
        'max_samples_per_queue' => 1000,

        /*
         * Recording a pickup costs a write on the hot path of every job. Since
         * only `max_samples_per_queue` entries survive, a high-throughput queue
         * spends that write producing samples the store trims away moments
         * later — and the survivors describe the last instant of the window
         * rather than the window as a whole.
         *
         * Above `max_per_second` each worker process forwards a uniformly
         * random subset instead, which costs less and covers the window better.
         * Set `enabled` to false to record every pickup.
         */
        'sampling' => [
            'enabled' => env('QUEUE_AUTOSCALE_PICKUP_SAMPLING', true),
            'max_per_second' => (int) env('QUEUE_AUTOSCALE_PICKUP_SAMPLES_PER_SECOND', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Spawn latency tracker (global)
    |--------------------------------------------------------------------------
    |
    | Controls the optional cross-process tracker used for spawn compensation.
    |
    | Supported values:
    | - 'auto'  => Redis in cluster mode, null/fallback in single-host mode
    | - 'redis' => force EMA latency tracking in Redis
    | - 'null'  => disable shared spawn-latency tracking
    | - FQCN    => custom SpawnLatencyTrackerContract implementation
    |
    */
    'spawn_latency' => [
        'tracker' => env('QUEUE_AUTOSCALE_SPAWN_LATENCY_TRACKER', 'auto'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Failure fuse (circuit breaker)
    |--------------------------------------------------------------------------
    |
    | A downstream outage looks exactly like load to an autoscaler: jobs fail,
    | get released, the backlog grows and the oldest job ages — so the naive
    | response is to add workers, which only hammers the failing dependency
    | harder and burns each job's retry budget faster.
    |
    | The fuse watches the recent failure rate per queue. Above the threshold
    | it trips, holds the queue at workers.min, and after a cooldown lets a
    | single worker probe for recovery before scaling is released again.
    |
    | Thresholds are per-queue and live in the profile ('fuse' block); the two
    | settings here are infrastructure.
    |
    | 'enabled' is a master switch — turning it off disables the fuse for every
    | queue regardless of profile.
    |
    | 'store' selects where outcome counters live. Job outcomes are counted in
    | the worker processes and read by the manager, so this must be a shared
    | backend even on a single host:
    | - 'auto'/'cache' => Laravel's cache (any driver: redis, database, file)
    | - 'null'         => disable outcome tracking (fuse never trips)
    | - FQCN           => custom FailureWindowStoreContract implementation
    |
    | 'ignored_exceptions' lists exception classes that say nothing about
    | capacity — a job that threw a validation error on its own payload never
    | reached the dependency, so holding the queue back over it would be
    | wrong. Matching is by instanceof, so a base class covers its subclasses.
    | Ignored exceptions are dropped entirely: they count neither as failures
    | nor as successes.
    |
    | Note that rate limits and auth errors are deliberately NOT ignored by
    | default. A job-level circuit breaker skips them because it wants the
    | retry to happen later; the autoscaler's question is whether adding
    | workers would help, and against a rate limit the answer is no.
    |
    | 'classifier' replaces that list-based logic wholesale when you need
    | per-queue or per-message decisions.
    |
    */
    'fuse' => [
        'enabled' => env('QUEUE_AUTOSCALE_FUSE_ENABLED', true),
        'store' => LandlordFailureWindowStore::class,

        'ignored_exceptions' => [
            // \App\Exceptions\InvalidPayloadException::class,
        ],

        'classifier' => ConfigurableFailureClassifier::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Scaling algorithm tuning (global)
    |--------------------------------------------------------------------------
    */
    'scaling' => [
        'fallback_job_time_seconds' => (float) env('QUEUE_AUTOSCALE_FALLBACK_JOB_TIME', 2.0),
        'breach_threshold' => 0.5,

        /*
         * Anti-flapping window. One-sided: it holds a scale-DOWN, never a
         * scale-up, and only while the window opened by a recent scale-up is
         * still running. Consecutive withdrawals are not delayed. Raising it
         * makes an over-provisioned fleet stay that way for longer; it does
         * not slow how fast the fleet responds to load.
         */
        'cooldown_seconds' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource limits (global)
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_cpu_percent' => 85,
        'max_memory_percent' => 85,
        // Workers sit at ~250 MB RSS; a lower estimate lets the cluster leader assign more workers than a task can hold.
        'worker_memory_mb_estimate' => 256,
        'worker_cpu_core_estimate' => 0.2,
        'reserve_cpu_cores' => 0.2,

        /*
         * Hard ceiling on total workers this host may run, across every queue
         * and group. null means no ceiling.
         *
         * Per-queue workers.min is applied AFTER the CPU/memory clamp, so a
         * floor always wins over measured capacity — by design, since the
         * floor is your decision. The consequence is that many queues each
         * with a small floor can oversubscribe a host, and queues are
         * DISCOVERED from metrics rather than only read from config: a few
         * thousand historical per-tenant queue names become a few thousand
         * workers. This is the backstop for that.
         */
        'max_total_workers' => is_numeric($maxTotalWorkers = env('QUEUE_AUTOSCALE_MAX_TOTAL_WORKERS')) ? (int) $maxTotalWorkers : null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Manager process
    |--------------------------------------------------------------------------
    |
    | restart_scope controls the cache key used by queue:autoscale:restart.
    | Leave unset unless multiple apps share the same cache backend.
    |
    | honor_queue_restart makes the manager also exit gracefully when
    | Laravel's own `php artisan queue:restart` signal is issued, so a
    | standard deploy pipeline restarts the manager without extra steps.
    |
    */
    'manager' => [
        'evaluation_interval_seconds' => 5,

        /*
         * How long a handover waits for the outgoing manager to finish
         * draining, and the floor for a pool-wide drain. This is about the
         * MANAGER process — a worker's own drain window is per-queue, in the
         * profile's workers.shutdown_timeout_seconds.
         */
        'shutdown_grace_seconds' => (int) env('QUEUE_AUTOSCALE_SHUTDOWN_GRACE', 90),
        'log_channel' => env('QUEUE_AUTOSCALE_LOG_CHANNEL', 'stderr'),
        'restart_scope' => env('QUEUE_AUTOSCALE_RESTART_SCOPE'),
        'honor_queue_restart' => env('QUEUE_AUTOSCALE_HONOR_QUEUE_RESTART', true),

        /*
         * On startup, SIGTERM workers left behind by a previous manager
         * generation (found via /proc by the env markers every spawned
         * worker carries, scoped to this manager's id). A manager killed
         * abruptly (e.g. by the OOM killer) cannot drain its pool, and the
         * replacement would otherwise spawn a full new set of workers on
         * top of the orphans.
         */
        'reap_orphans_on_start' => env('QUEUE_AUTOSCALE_REAP_ORPHANS_ON_START', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cluster coordination
    |--------------------------------------------------------------------------
    |
    | Single-host mode does NOT require Redis and continues to work with any
    | supported queue driver (database, redis, SQS, etc.).
    |
    | Enable this when you run the autoscale manager on multiple hosts against
    | the same queues. Managers auto-join the cluster via Redis, elect a
    | leader, publish local host capacity/state, and receive per-host worker
    | recommendations from the leader.
    |
    | Redis is required for cluster mode.
    |
    | On by default everywhere, including locally (one manager simply leads itself), so every worker, the scheduler
    | and the manager agree on it. It also enables the Redis pickup-time store the p95 pickup signal needs.
    |
    */
    'cluster' => [
        'enabled' => env('QUEUE_AUTOSCALE_CLUSTER_ENABLED', true),
        // The leader renews its lease each evaluation cycle, so the lease must comfortably exceed the slowest cycle or
        // leadership flaps. The package minimum of 15s is short enough to flap fleet-wide under a slow Redis SCAN.
        'heartbeat_ttl_seconds' => (int) env('QUEUE_AUTOSCALE_CLUSTER_HEARTBEAT_TTL', 45),
        'leader_lease_seconds' => (int) env('QUEUE_AUTOSCALE_CLUSTER_LEADER_LEASE', 60),
        'recommendation_ttl_seconds' => (int) env('QUEUE_AUTOSCALE_CLUSTER_RECOMMENDATION_TTL', 30),
        'summary_ttl_seconds' => (int) env('QUEUE_AUTOSCALE_CLUSTER_SUMMARY_TTL', 30),
        'decision_history_seconds' => (int) env('QUEUE_AUTOSCALE_DECISION_HISTORY', 3600),
        'decision_history_max' => (int) env('QUEUE_AUTOSCALE_DECISION_HISTORY_MAX', 10000),
    ],

    'strategy' => HybridStrategy::class,

    'policies' => [
        ConservativeScaleDownPolicy::class,
        BreachNotificationPolicy::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert rate limiting
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) to suppress repeated alerts for the same queue
    | and signal. Applies to the built-in BreachNotificationPolicy and any
    | listener that injects the AlertRateLimiter.
    |
    | A breach that stays active will log once, then go quiet for this many
    | seconds. Flapping alerts are suppressed too.
    |
    */
    'alerting' => [
        'cooldown_seconds' => (int) env('QUEUE_AUTOSCALE_ALERT_COOLDOWN', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | 📡 TELEMETRY (cboxdk/laravel-telemetry)
    |--------------------------------------------------------------------------
    |
    | When cboxdk/laravel-telemetry is installed, the autoscaler automatically
    | publishes scaling gauges/counters and pushes domain events (scaling
    | actions, SLA breaches, leader changes) to it, plus observable cluster
    | gauges in cluster mode. Batteries included: on by default, no-op when
    | the package is not installed.
    |
    */
    'telemetry' => [
        'enabled' => env('QUEUE_AUTOSCALE_TELEMETRY_ENABLED', false),

        'cache_ttl' => (int) env('QUEUE_AUTOSCALE_TELEMETRY_CACHE_TTL', 10),

        /*
         * Queue names appear as a metric label. Because queues are discovered
         * rather than listed, an application that names them per tenant can
         * present thousands — one time series per tenant per metric, which is
         * how a Prometheus or OTel backend falls over or produces a very large
         * bill. Queue names embedding tenant identifiers also land in a system
         * with a different access-control model than the app.
         *
         * Above this many distinct queues, further ones share an "__other__"
         * label rather than minting new series. Set to null for no cap.
         */
        'max_queue_labels' => (int) env('QUEUE_AUTOSCALE_TELEMETRY_MAX_QUEUE_LABELS', 100),

        'gauges' => [
            'cluster' => true,
        ],

        'events' => true,
    ],
];
