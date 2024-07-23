<?php

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiMessageFile;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Ai\Events\AiMessageForceDeleting;
use AdvisingApp\Ai\Listeners\AiMessageCascadeForceDeletingAiMessageFiles;

it('force deletes related AiMessageFiles when an AiMessage is deleted', function () {
    $aiMessage = AiMessage::factory()
        ->has(AiMessageFile::factory(), 'files')
        ->create();

    assertDatabaseCount('ai_message_files', 1);

    Event::fake();

    (new AiMessageCascadeForceDeletingAiMessageFiles())->handle(new AiMessageForceDeleting($aiMessage));

    assertDatabaseCount('ai_message_files', 0);
});
