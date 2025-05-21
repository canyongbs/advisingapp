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
