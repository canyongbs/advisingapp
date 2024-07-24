<?php

use function Pest\Laravel\artisan;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

use Illuminate\Database\Console\PruneCommand;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;

it('properly prunes AiThread models', function (AiThread $thread, bool $shouldPrune) {
    assertTrue($thread->exists);

    Event::fake();

    artisan(PruneCommand::class, [
        '--model' => AiThread::class,
    ]);

    if ($shouldPrune) {
        assertNull($thread->fresh());

        Event::assertDispatched(AiThreadForceDeleting::class);
    } else {
        assertTrue($thread->fresh()->exists);

        Event::assertNotDispatched(AiThreadForceDeleting::class);
    }
})->with([
    'Not soft deleted' => [
        fn () => AiThread::factory()->create(),
        false,
    ],
    'Not soft deleted with messages' => [
        fn () => AiThread::factory()->has(AiMessage::factory(), 'messages')->create(),
        false,
    ],
    'Soft deleted recently' => [
        fn () => AiThread::factory()->create(['deleted_at' => now()]),
        false,
    ],
    'Soft deleted recently with messages' => [
        fn () => AiThread::factory()->has(AiMessage::factory(), 'messages')->create(['deleted_at' => now()]),
        false,
    ],
    'Soft deleted more than a week ago' => [
        fn () => AiThread::factory()->create(['deleted_at' => now()->subDays(8)]),
        true,
    ],
    'Soft deleted more than a week ago with messages' => [
        fn () => AiThread::factory()->has(AiMessage::factory(), 'messages')->create(['deleted_at' => now()->subDays(8)]),
        false,
    ],
    'Soft deleted more than a week ago with soft deleted messages' => [
        fn () => AiThread::factory()->has(AiMessage::factory()->state(['deleted_at' => now()]), 'messages')->create(['deleted_at' => now()->subDays(8)]),
        false,
    ],
]);
