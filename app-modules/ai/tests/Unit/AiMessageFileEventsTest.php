<?php

use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;

it('dispatches the AiMessageFileDeleted event when an AiMessageFile is deleted', function () {
    $aiMessageFile = AiMessageFile::factory()->create();

    Event::fake();

    $aiMessageFile->delete();

    Event::assertDispatched(AiMessageFileDeleted::class, function ($event) use ($aiMessageFile) {
        return $event->aiMessageFile->is($aiMessageFile);
    });
});

it('dispatches the AiMessageFileDeleted event when an AiMessageFile is force deleted', function () {
    $aiMessageFile = AiMessageFile::factory()->create();

    Event::fake();

    $aiMessageFile->forceDelete();

    Event::assertDispatched(AiMessageFileDeleted::class, function ($event) use ($aiMessageFile) {
        return $event->aiMessageFile->is($aiMessageFile);
    });
});
