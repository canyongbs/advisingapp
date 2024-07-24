<?php

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageFileForceDeleting;
use AdvisingApp\Ai\Listeners\DeleteExternalAiMessageFile;

it('dispatches the AiMessageFileForceDeleting event when an AiMessageFile is force deleting', function () {
    $aiMessageFile = AiMessageFile::factory()
        ->has(
            factory: AiMessage::factory()
                ->has(
                    factory: AiThread::factory()
                        ->has(AiAssistant::factory(), 'assistant'),
                    relationship: 'thread'
                ),
            relationship: 'message',
        )
        ->create();

    Event::fake();

    $aiMessageFile->forceDelete();

    Event::assertDispatched(AiMessageFileForceDeleting::class, function (AiMessageFileForceDeleting $event) use ($aiMessageFile) {
        return $event->aiMessageFile->is($aiMessageFile);
    });

    Event::assertListening(
        expectedEvent: AiMessageFileForceDeleting::class,
        expectedListener: DeleteExternalAiMessageFile::class
    );
});
