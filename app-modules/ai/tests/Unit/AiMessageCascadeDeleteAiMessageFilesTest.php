<?php

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Support\Facades\Event;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageTrashed;

use function Pest\Laravel\assertDatabaseCount;

use AdvisingApp\Ai\Listeners\AiMessageCascadeDeleteAiMessageFiles;

it('soft deletes related AiMessageFiles when an AiMessage is deleted', function () {
    $aiMessage = AiMessage::factory()
        ->has(AiMessageFile::factory(), 'files')
        ->create([
            'deleted_at' => now(),
        ]);

    assertDatabaseCount('ai_message_files', 1);

    $aiMessage->files->each(fn (AiMessageFile $file) => expect($file->trashed())->toBeFalse());

    Event::fake();

    (new AiMessageCascadeDeleteAiMessageFiles())->handle(new AiMessageTrashed($aiMessage));

    assertDatabaseCount('ai_messages', 1);

    $aiMessage->files->each(fn (AiMessageFile $file) => expect($file->fresh()->trashed())->toBeTrue());
});
