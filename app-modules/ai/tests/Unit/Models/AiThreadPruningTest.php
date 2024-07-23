<?php

use function Pest\Laravel\artisan;

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseHas;

use Illuminate\Database\Console\PruneCommand;
use AdvisingApp\Ai\Events\AiThreadForceDeleted;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;

use function Pest\Laravel\assertDatabaseMissing;

it('properly prunes AiThread models', function (AiThread $thread, bool $shouldPrune) {
    assertDatabaseHas(AiThread::class, [
        'id' => $thread->id,
    ]);

    Event::fake();

    artisan(PruneCommand::class, [
        '--model' => AiThread::class,
    ]);

    if ($shouldPrune) {
        assertDatabaseMissing(AiThread::class, [
            'id' => $thread->id,
        ]);

        Event::assertDispatched(AiThreadForceDeleting::class);
        Event::assertDispatched(AiThreadForceDeleted::class);
    } else {
        assertDatabaseHas(AiThread::class, [
            'id' => $thread->id,
        ]);

        Event::assertNotDispatched(AiThreadForceDeleting::class);
        Event::assertNotDispatched(AiThreadForceDeleted::class);
    }
})->with([
    'Saved Thread' => [
        fn () => AiThread::factory()->saved()->create(),
        false,
    ],
    'Soft Deleted' => [
        fn () => AiThread::factory()->create(['deleted_at' => now()]),
        true,
    ],
]);
