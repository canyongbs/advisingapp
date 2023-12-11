<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
    | Rest Gates
    |--------------------------------------------------------------------------
    |
    | The following configuration option contains gates customisation. You might
    | want to adapt this feature to your needs.
    |
    */

    'gates' => [
        'enabled' => true,
        'key' => 'gates',
        // Here you can customize the keys for each gate
        'names' => [
            'authorized_to_view' => 'authorized_to_view',
            'authorized_to_create' => 'authorized_to_create',
            'authorized_to_update' => 'authorized_to_update',
            'authorized_to_delete' => 'authorized_to_delete',
            'authorized_to_restore' => 'authorized_to_restore',
            'authorized_to_force_delete' => 'authorized_to_force_delete',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rest Authorizations
    |--------------------------------------------------------------------------
    |
    | This is the feature that automatically binds to policies to validate incoming requests.
    | Laravel Rest Api will validate each models searched / mutated / deleted to avoid leaks in your API.
    |
    */

    'authorizations' => [
        'enabled' => true,
        'cache' => [
            'enabled' => true,
            'default' => 5, // Cache minutes by default
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rest Documentation
    |--------------------------------------------------------------------------
    |
    | This is the feature that generates automatically your API documentation for you.
    | Laravel Rest Api will validate each models searched / mutated / deleted to avoid leaks in your API.
    | This feature is based on OpenApi, for more detail see: https://swagger.io/specification/
    |
    */

    'documentation' => [
        'routing' => [
            'enabled' => true,
            'domain' => null,
            'path' => '/api-documentation',
            'middlewares' => [
                'web',
            ],
        ],
        'info' => [
            'title' => 'Advising App™ REST API',
            'summary' => '',
            'description' => 'Advising App™ created by Canyon GBS™ API Documentation',
            'termsOfService' => null, // (Optional) Url to terms of services
            'contact' => [
                'name' => '',
                'email' => '',
                'url' => '',
            ],
            'license' => [
                'url' => null,
                'name' => 'Elastic License 2.0',
                'identifier' => 'Elastic-2.0',
            ],
            'version' => '0.1.0',
        ],
        // See https://spec.openapis.org/oas/v3.1.0#server-object
        'servers' => [
            //[
            //    'url' => env('APP_URL'), // Relative to current
            //    'description' => '',
            //],
            //  [
            //      'url' => '"https://my-server.com:{port}/{basePath}"',
            //      'description' => 'Production server',
            //      'variables' => [
            //          'port' => [
            //              'enum' => ['80', '443'],
            //              'default' => '443'
            //           ],
            //           'basePath' => [
            //              'default' => 'v2',
            //              'enum' => ['v1', 'v2'],
            //           ]
            //       ]
            //  ]
        ],
        // See https://spec.openapis.org/oas/v3.1.0#security-scheme-object
        'security' => [
            //  [
            //      'type' => 'http',
            //      'description' => 'description',
            //      'scheme' => 'Bearer',
            //      'bearerFormat' => 'JWT'
            //  ],
            //  [
            //       'type' => 'oauth2',
            //       'flows' => [
            //          'authorizationCode' => [
            //              'scopes' => ['write:pets'],
            //              'tokenUrl' => 'https://example.com/api/oauth/token',
            //              'authorizationUrl' => 'https://example.com/api/oauth/dialog',
            //              'refreshUrl' => 'https://example.com/api/oauth/refresh',
            //          ]
            //       ]
            //  ]
        ],
    ],
];
