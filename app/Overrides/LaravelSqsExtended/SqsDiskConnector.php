<?php

declare(strict_types = 1);

namespace App\Overrides\LaravelSqsExtended;

use Aws\Sqs\SqsClient;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Queue\Queue;
use DefectiveCode\LaravelSqsExtended\SqsDiskConnector as BaseSqsDiskConnector;

class SqsDiskConnector extends BaseSqsDiskConnector
{
    /**
     * Establish a queue connection.
     *
     *
     * @return Queue
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return new SqsDiskQueue(
            new SqsClient(
                Arr::except($config, ['token'])
            ),
            $config['queue'],
            $config['disk_options'],
            $config['prefix'] ?? '',
            $config['suffix'] ?? '',
            $config['after_commit'] ?? null,
        );
    }
}
