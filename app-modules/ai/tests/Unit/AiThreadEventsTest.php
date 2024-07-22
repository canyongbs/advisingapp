<?php

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Events\AiThreadTrashed;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Listeners\AiThreadCascadeDeleteAiMessages;

it('dispatches the AiThreadTrashed event when an AiThread is deleted', function () {
    $aiThread = AiThread::factory()->create();

    Event::fake();

    $aiThread->delete();

    Event::assertDispatched(AiThreadTrashed::class, function (AiThreadTrashed $event) use ($aiThread) {
        return $event->aiThread->is($aiThread);
    });

    Event::assertListening(
        expectedEvent: AiThreadTrashed::class,
        expectedListener: AiThreadCascadeDeleteAiMessages::class
    );
});

it('dispatches the AiThreadForceDeleting event when an AiThread is force deleted', function () {
    $aiThread = AiThread::factory()->create();

    Event::fake();

    $aiThread->forceDelete();

    Event::assertDispatched(AiThreadForceDeleting::class, function (AiThreadForceDeleting $event) use ($aiThread) {
        return $event->aiThread->is($aiThread);
    });

    Event::assertListening(
        expectedEvent: AiThreadForceDeleting::class,
        expectedListener: AiThreadCascadeDeleteAiMessages::class
    );
});
