<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenSearch\Laravel\Client\ClientBuilderInterface;

class OpenSearchDeleteAllIndices extends Command
{
    protected $signature = 'opensearch:clear-indices';

    protected $description = 'Clear all indices in OpenSearch.';

    public function handle(ClientBuilderInterface $clientBuilder): int
    {
        if (config('scout.driver') !== 'opensearch') {
            $this->error('Scout driver must be set to opensearch.');

            return self::FAILURE;
        }

        $client = $clientBuilder->default();

        $response = $client->indices()->delete(
            [
                'index' => '*',
            ]
        );

        $this->info(json_encode($response, JSON_PRETTY_PRINT));

        return self::SUCCESS;
    }
}
