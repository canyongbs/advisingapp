<?php

use function Pest\Laravel\artisan;

use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiMessageFile;

use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

use Illuminate\Database\Console\PruneCommand;

it('properly prunes AiMessageFile models', function (AiMessageFile $messageFile, bool $shouldPrune) {
    assertTrue($messageFile->exists);

    Event::fake();

    artisan(PruneCommand::class, [
        '--model' => AiMessageFile::class,
    ]);

    if ($shouldPrune) {
        assertNull($messageFile->fresh());
    } else {
        assertTrue($messageFile->fresh()->exists);
    }
})->with([
    'Not soft deleted' => [
        fn () => AiMessageFile::factory()->create(),
        false,
    ],
    'Soft deleted recently' => [
        fn () => AiMessageFile::factory()->create(['deleted_at' => now()]),
        false,
    ],
    'Soft deleted more than a week ago' => [
        fn () => AiMessageFile::factory()->create(['deleted_at' => now()->subDays(8)]),
        true,
    ],
]);
