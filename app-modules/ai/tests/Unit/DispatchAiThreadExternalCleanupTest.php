<?php

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Events\AiThreadForceDeleted;
use AdvisingApp\Ai\Jobs\DeleteExternalAiThread;
use AdvisingApp\Ai\Jobs\DeleteAiThreadVectorStores;
use AdvisingApp\Ai\Listeners\DispatchAiThreadExternalCleanup;

it('dispatches the correct jobs', function () {
    $aiThread = AiThread::factory()->create();

    Bus::fake();

    (new DispatchAiThreadExternalCleanup())->handle(new AiThreadForceDeleted($aiThread));

    Bus::assertChained([
        DeleteAiThreadVectorStores::class,
        DeleteExternalAiThread::class,
    ]);
});
