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

use Illuminate\Support\Str;

$redisClusterSeedNode = [
    'scheme' => env('REDIS_SCHEME', 'tcp'),
    'url' => env('REDIS_URL'),
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'username' => env('REDIS_USERNAME'),
    'password' => env('REDIS_PASSWORD'),
    'port' => env('REDIS_PORT', '6379'),
];

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'landlord'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [
        'landlord' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'tenant' => [
            'driver' => 'pgsql',
            'host' => env('TENANT_DB_HOST'),
            'port' => env('TENANT_DB_PORT'),
            'database' => env('TENANT_DB_DATABASE', ''),
            'username' => env('TENANT_DB_USERNAME'),
            'password' => env('TENANT_DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Every Redis connection targets the same cluster: ElastiCache Serverless in staging and
    | production (which always runs in cluster mode), and a local multi-node cluster in development
    | and CI. Cluster mode only has database 0 and rejects SELECT, so no connection sets a database
    | index, and multi-key operations rely on {hash tag} key names to stay within a single slot.
    |
    */

    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
            // PhpRedis defaults both to 0 (unbounded), so a node that stalls mid-response would hang the request.
            'timeout' => (float) env('REDIS_TIMEOUT', 5),
            'read_timeout' => (float) env('REDIS_READ_TIMEOUT', 5),
            // Rides out ElastiCache Serverless slot migrations and transient drops instead of surfacing them as errors.
            'max_retries' => (int) env('REDIS_MAX_RETRIES', 5),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => (int) env('REDIS_BACKOFF_BASE', 50),
            'backoff_cap' => (int) env('REDIS_BACKOFF_CAP', 1000),
            // OPT_TCP_KEEPALIVE is deliberately not set: setting it on a RedisCluster client segfaults phpredis 6.3.0.
            'persistent' => (bool) env('REDIS_PERSISTENT', true),
            // 0 = FAILOVER_NONE: all traffic goes to the writer, keeping strong read-after-write consistency.
            'failover' => (int) env('REDIS_FAILOVER', 0),
            // Nodes discovered via CLUSTER SLOTS have no scheme, so without this context phpredis connects to them in plaintext.
            ...(env('REDIS_SCHEME', 'tcp') === 'tls' ? ['context' => ['ssl' => [
                'verify_peer' => (bool) env('REDIS_VERIFY_PEER', true),
                'verify_peer_name' => (bool) env('REDIS_VERIFY_PEER', true),
            ]]] : []),
        ],

        // phpredis discovers the rest of the cluster topology from the seed node via CLUSTER SLOTS.
        'clusters' => [
            'default' => [$redisClusterSeedNode],

            'cache' => [$redisClusterSeedNode],

            'session' => [$redisClusterSeedNode],
        ],
    ],
];
