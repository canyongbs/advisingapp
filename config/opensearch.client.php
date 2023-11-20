<?php

declare(strict_types = 1);

return [
    'default' => env('OPENSEARCH_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [
                env('OPENSEARCH_HOST', 'localhost:9200'),
            ],
            'basicAuthentication' => [env('OPENSEARCH_USERNAME', 'admin'), env('OPENSEARCH_PASSWORD', 'admin')],
            'retries' => (int) env('OPENSEARCH_RETRYS', 2),
            'sigV4Region' => env('OPENSEARCH_REGION'),
            'sigV4Service' => env('OPENSEARCH_SERVICE'),
            'sigV4CredentialProvider' => [
                'key' => env('OPENSEARCH_IAM_KEY'),
                'secret' => env('OPENSEARCH_IAM_SECRET'),
            ],
        ],
    ],
];
