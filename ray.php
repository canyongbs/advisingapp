<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    * This setting controls whether data should be sent to Ray.
    *
    * By default, `ray()` will only transmit data in non-production environments.
    */
    'enable' => env('RAY_ENABLED', true),

    /*
    * When enabled, all cache events  will automatically be sent to Ray.
    */
    'send_cache_to_ray' => env('SEND_CACHE_TO_RAY', false),

    /*
    * When enabled, all things passed to `dump` or `dd`
    * will be sent to Ray as well.
    */
    'send_dumps_to_ray' => env('SEND_DUMPS_TO_RAY', true),

    /*
    * When enabled all job events will automatically be sent to Ray.
    */
    'send_jobs_to_ray' => env('SEND_JOBS_TO_RAY', false),

    /*
    * When enabled, all things logged to the application log
    * will be sent to Ray as well.
    */
    'send_log_calls_to_ray' => env('SEND_LOG_CALLS_TO_RAY', true),

    /*
    * When enabled, all queries will automatically be sent to Ray.
    */
    'send_queries_to_ray' => env('SEND_QUERIES_TO_RAY', false),

    /**
     * When enabled, all duplicate queries will automatically be sent to Ray.
     */
    'send_duplicate_queries_to_ray' => env('SEND_DUPLICATE_QUERIES_TO_RAY', false),

    /*
     * When enabled, slow queries will automatically be sent to Ray.
     */
    'send_slow_queries_to_ray' => env('SEND_SLOW_QUERIES_TO_RAY', false),

    /**
     * Queries that are longer than this number of milliseconds will be regarded as slow.
     */
    'slow_query_threshold_in_ms' => env('RAY_SLOW_QUERY_THRESHOLD_IN_MS', 500),

    /*
    * When enabled, all requests made to this app will automatically be sent to Ray.
    */
    'send_requests_to_ray' => env('SEND_REQUESTS_TO_RAY', false),

    /**
     * When enabled, all Http Client requests made by this app will be automatically sent to Ray.
     */
    'send_http_client_requests_to_ray' => env('SEND_HTTP_CLIENT_REQUESTS_TO_RAY', false),

    /*
    * When enabled, all views that are rendered automatically be sent to Ray.
    */
    'send_views_to_ray' => env('SEND_VIEWS_TO_RAY', false),

    /*
     * When enabled, all exceptions will be automatically sent to Ray.
     */
    'send_exceptions_to_ray' => env('SEND_EXCEPTIONS_TO_RAY', true),

    /*
     * When enabled, all deprecation notices will be automatically sent to Ray.
     */
    'send_deprecated_notices_to_ray' => env('SEND_DEPRECATED_NOTICES_TO_RAY', false),

    /*
    * The host used to communicate with the Ray app.
    * When using Docker on Mac or Windows, you can replace localhost with 'host.docker.internal'
    * When using Docker on Linux, you can replace localhost with '172.17.0.1'
    * When using Homestead with the VirtualBox provider, you can replace localhost with '10.0.2.2'
    * When using Homestead with the Parallels provider, you can replace localhost with '10.211.55.2'
    */
    'host' => env('RAY_HOST', 'host.docker.internal'),

    /*
    * The port number used to communicate with the Ray app.
    */
    'port' => env('RAY_PORT', 23517),

    /*
     * Absolute base path for your sites or projects in Homestead,
     * Vagrant, Docker, or another remote development server.
     */
    'remote_path' => env('RAY_REMOTE_PATH', null),

    /*
     * Absolute base path for your sites or projects on your local
     * computer where your IDE or code editor is running on.
     */
    'local_path' => env('RAY_LOCAL_PATH', null),

    /*
     * When this setting is enabled, the package will not try to format values sent to Ray.
     */
    'always_send_raw_values' => false,
];
