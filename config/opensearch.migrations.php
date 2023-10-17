<?php

declare(strict_types = 1);

return [
    'storage' => [
        'default_path' => env('OPENSEARCH_MIGRATIONS_DEFAULT_PATH', base_path('opensearch/migrations')),
    ],
    'database' => [
        'table' => env('OPENSEARCH_MIGRATIONS_TABLE', 'opensearch_migrations'),
        'connection' => env('OPENSEARCH_MIGRATIONS_CONNECTION'),
    ],
    'prefixes' => [
        'index' => env('OPENSEARCH_MIGRATIONS_INDEX_PREFIX', env('SCOUT_PREFIX', '')),
        'alias' => env('OPENSEARCH_MIGRATIONS_ALIAS_PREFIX', env('SCOUT_PREFIX', '')),
    ],
];
