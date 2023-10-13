<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenSearch\Laravel\Client\ClientBuilderInterface;

class OpenSearchList extends Command
{
    protected $signature = 'app:open-search-list-indexes';

    protected $description = 'List all indexes in OpenSearch.';

    public function handle(ClientBuilderInterface $clientBuilder): int
    {
        if (config('scout.driver') !== 'opensearch') {
            $this->error('Scout driver must be set to opensearch.');

            return 1;
        }

        $client = $clientBuilder->default();

        $indices = $client->indices()->get(
            [
                'index' => '*',
            ]
        );

        $this->info(json_encode($indices, JSON_PRETTY_PRINT));

        return 0;
    }
}
