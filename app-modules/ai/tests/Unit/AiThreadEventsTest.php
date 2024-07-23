<?php

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Events\AiThreadTrashed;
use AdvisingApp\Ai\Events\AiThreadForceDeleted;
use AdvisingApp\Ai\Events\AiThreadForceDeleting;
use AdvisingApp\Ai\Listeners\AiThreadCascadeDeleteAiMessages;
use AdvisingApp\Ai\Listeners\DispatchAiThreadExternalCleanup;

it('dispatches the AiThreadTrashed event when an AiThread is deleted', function () {
    $aiThread = AiThread::factory()->create();

    Event::fake();

    $aiThread->delete();

    Event::assertDispatched(AiThreadTrashed::class, function (AiThreadTrashed $event) use ($aiThread) {
        return $event->aiThread->is($aiThread) && $event->aiThread->trashed();
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

    // Event::assertListening(
    //     expectedEvent: AiThreadForceDeleting::class,
    //     expectedListener: AiThreadCascadeForceDeletingAiMessages::class
    // );
});

it('dispatches the AiThreadForceDeleted event when an AiThread is force deleted', function () {
    $aiThread = AiThread::factory()->create();

    Event::fake();

    $aiThread->forceDelete();

    Event::assertDispatched(AiThreadForceDeleted::class, function (AiThreadForceDeleted $event) use ($aiThread) {
        return $event->aiThread->is($aiThread);
    });

    Event::assertListening(
        expectedEvent: AiThreadForceDeleted::class,
        expectedListener: DispatchAiThreadExternalCleanup::class
    );
});
