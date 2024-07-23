<?php

use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Events\AiMessageFileDeleted;
use AdvisingApp\Ai\Jobs\DeleteExternalAiMessageFile;
use AdvisingApp\Ai\Listeners\DispatchDeleteExternalAiMessageFile;

it('dispatches the correct DeleteExternalAiMessageFile job', function () {
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

    Bus::fake();

    AiMessageFile::preventLazyLoading();

    $listener = new DispatchDeleteExternalAiMessageFile();
    $listener->handle(new AiMessageFileDeleted($aiMessageFile));

    Bus::assertDispatched(DeleteExternalAiMessageFile::class, function (DeleteExternalAiMessageFile $job) use ($aiMessageFile) {
        return $job->aiMessageFile->is($aiMessageFile)
            && ! empty($job->aiMessageFile->relationsToArray()['message']['thread']['assistant']['model']);
    });
});
