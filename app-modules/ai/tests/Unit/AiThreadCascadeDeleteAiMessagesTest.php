<?php

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Events\AiThreadTrashed;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Ai\Listeners\AiThreadCascadeDeleteAiMessages;

it('soft deletes related AiMessages when an AiThread is deleted', function () {
    $aiThread = AiThread::factory()
        ->has(AiMessage::factory()->count(3), 'messages')
        ->create([
            'deleted_at' => now(),
        ]);

    assertDatabaseCount('ai_messages', 3);

    $aiThread->messages->each(fn (AiMessage $message) => expect($message->trashed())->toBeFalse());

    Event::fake();

    (new AiThreadCascadeDeleteAiMessages())->handle(new AiThreadTrashed($aiThread));

    assertDatabaseCount('ai_messages', 3);

    $aiThread->messages->each(fn (AiMessage $message) => expect($message->fresh()->trashed())->toBeTrue());
});
