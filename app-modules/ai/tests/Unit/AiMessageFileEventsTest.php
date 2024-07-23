<?php

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\Ai\Listeners\DispatchDeleteExternalAiMessageFile;

it('dispatches the AiMessageFileDeleted event when an AiMessageFile is deleted', function () {
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

    $aiMessageFile->delete();

    Event::assertDispatched(AiMessageFileDeleted::class, function (AiMessageFileDeleted $event) use ($aiMessageFile) {
        return $event->aiMessageFile->is($aiMessageFile);
    });

    Event::assertListening(
        expectedEvent: AiMessageFileDeleted::class,
        expectedListener: DispatchDeleteExternalAiMessageFile::class
    );
});
