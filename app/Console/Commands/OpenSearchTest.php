<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenSearch\Laravel\Client\ClientBuilderInterface;

class OpenSearchTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:open-search-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(ClientBuilderInterface $clientBuilder)
    {
        $client = $clientBuilder->default();

        $indices = $client->indices()->get(
            [
                'index' => '*',
            ]
        );

        ray($indices);
    }
}
