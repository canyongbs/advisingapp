<?php

namespace AdvisingApp\Authorization\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TestAzureAvatarFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorization:test-azure-avatar-fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Azure avatar fetch and error handling (404)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Http::fake([
            'graph.microsoft.com/v1.0/me/photo/$value' => Http::response('', 404),
        ]);

        $exceptionThrown = false;

        try {
            $request = Http::withToken('test')
                        // ->retry(3, 500)
                ->get('https://graph.microsoft.com/v1.0/me/photo/$value')
                ->throwIf(fn (Response $response) => $response->failed() && $response->status() !== 404);

            if ($request->status() === 404) {
                $this->info('Photo not found (404) — no exception thrown.');
            } else {
                $this->warn("Received status: {$request->status()}");
            }
        } catch (Exception $e) {
            $exceptionThrown = true;
            $this->error('Exception was thrown: ' . $e->getMessage());
        }

        if (! $exceptionThrown) {
            $this->info('Test passed. No exception was thrown for 404.');
        }

        return 0;
    }
}
