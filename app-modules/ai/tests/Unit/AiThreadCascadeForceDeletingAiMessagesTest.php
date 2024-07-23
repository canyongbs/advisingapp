<?php

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Listeners\AiThreadCascadeForceDeletingAiMessages;

it('force deletes related AiMessages when an AiThread is deleted', function () {
    $aiThread = AiThread::factory()
        ->has(AiMessage::factory()->count(3), 'messages')
        ->create();

    assertDatabaseCount('ai_messages', 3);

    Event::fake();

    (new AiThreadCascadeForceDeletingAiMessages())->handle(new AiThreadForceDeleting($aiThread));

    assertDatabaseCount('ai_messages', 0);
});
