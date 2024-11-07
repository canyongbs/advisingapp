<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Laravel's queue API supports an assortment of back-ends via a single
    | API, giving you convenient access to each back-end using the same
    | syntax for every one. Here you may define a default connection.
    |
    */

    'default' => env('QUEUE_CONNECTION', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    | Drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => 'localhost',
            'queue' => 'default',
            'retry_after' => 90,
            'block_for' => 0,
            'after_commit' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | SQS Disk Queue Configuration
        |--------------------------------------------------------------------------
        |
        |
        | always_store: Determines if all payloads should be stored on a disk regardless if they are over SQS's 256KB limit.
        | cleanup:      Determines if the payload files should be removed from the disk once the job is processed. Leaveing the
        |                 files behind can be useful to replay the queue jobs later for debugging reasons.
        | disk:         The disk to save SQS payloads to.  This disk should be configured in your Laravel filesystems.php config file.
        | prefix        The prefix (folder) to store the payloads with.  This is useful if you are sharing a disk with other SQS queues.
        |                 Using a prefix allows for the queue:clear command to destroy the files separately from other sqs-disk backed queues
        |                 sharing the same disk.
        |
        */
        'sqs' => [
            'driver' => 'canyongbs-sqs-disk',
            'key' => env('AWS_SQS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SQS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_SQS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
            'disk_options' => [
                'always_store' => true,
                'cleanup' => true,
                'disk' => env('FILESYSTEM_DISK', 'local'),
                'prefix' => 'sqs-payloads',
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

    'landlord_queue' => env('LANDLORD_SQS_QUEUE', 'landlord'),

    'outbound_communication_queue' => env('OUTBOUND_COMMUNICATION_QUEUE', env('SQS_QUEUE', 'default')),

    'import_export_queue' => env('IMPORT_EXPORT_QUEUE', env('SQS_QUEUE', 'default')),
];
