<?php

use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\Ai\Listeners\DeleteExternalAiMessageFile;

it('has the proper listeners registered', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: AiMessageFileDeleted::class,
        expectedListener: DeleteExternalAiMessageFile::class
    );
});
