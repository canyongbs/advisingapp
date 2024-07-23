<?php

use function Pest\Laravel\artisan;

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiMessageFile;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

use Illuminate\Database\Console\PruneCommand;

it('properly prunes AiMessage models', function (AiMessage $message, bool $shouldPrune) {
    assertTrue($message->exists);

    Event::fake();

    artisan(PruneCommand::class, [
        '--model' => AiMessage::class,
    ]);

    if ($shouldPrune) {
        assertNull($message->fresh());
    } else {
        assertTrue($message->fresh()->exists);
    }
})->with([
    'Not soft deleted' => [
        fn () => AiMessage::factory()->create(),
        false,
    ],
    'Not soft deleted with a file' => [
        fn () => AiMessage::factory()->has(AiMessageFile::factory(), 'files')->create(),
        false,
    ],
    'Soft deleted recently' => [
        fn () => AiMessage::factory()->create(['deleted_at' => now()]),
        false,
    ],
    'Soft deleted recently with a file' => [
        fn () => AiMessage::factory()->has(AiMessageFile::factory(), 'files')->create(['deleted_at' => now()]),
        false,
    ],
    'Soft deleted more than a week ago' => [
        fn () => AiMessage::factory()->create(['deleted_at' => now()->subDays(8)]),
        true,
    ],
    'Soft deleted more than a week ago with messages' => [
        fn () => AiMessage::factory()->has(AiMessageFile::factory(), 'files')->create(['deleted_at' => now()->subDays(8)]),
        false,
    ],
    'Soft deleted more than a week ago with a soft deleted file' => [
        fn () => AiMessage::factory()->has(AiMessageFile::factory()->state(['deleted_at' => now()]), 'files')->create(['deleted_at' => now()->subDays(8)]),
        false,
    ],
]);
