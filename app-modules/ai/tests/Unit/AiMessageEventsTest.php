<?php

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Events\AiMessageTrashed;
use AdvisingApp\Ai\Events\AiMessageForceDeleting;
use AdvisingApp\Ai\Listeners\AiMessageCascadeDeleteAiMessageFiles;
use AdvisingApp\Ai\Listeners\AiMessageCascadeForceDeletingAiMessageFiles;

it('dispatches the AiMessageTrashed event when an AiMessage is deleted', function () {
    $aiMessage = AiMessage::factory()->create();

    Event::fake();

    $aiMessage->delete();

    Event::assertDispatched(AiMessageTrashed::class, function (AiMessageTrashed $event) use ($aiMessage) {
        return $event->aiMessage->is($aiMessage) && $event->aiMessage->trashed();
    });

    Event::assertListening(
        expectedEvent: AiMessageTrashed::class,
        expectedListener: AiMessageCascadeDeleteAiMessageFiles::class
    );
});

it('dispatches the AiMessageForceDeleting event when an AiMessage is force deleted', function () {
    $aiMessage = AiMessage::factory()->create();

    Event::fake();

    $aiMessage->forceDelete();

    Event::assertDispatched(AiMessageForceDeleting::class, function (AiMessageForceDeleting $event) use ($aiMessage) {
        return $event->aiMessage->is($aiMessage);
    });

    Event::assertListening(
        expectedEvent: AiMessageForceDeleting::class,
        expectedListener: AiMessageCascadeForceDeletingAiMessageFiles::class
    );
});
