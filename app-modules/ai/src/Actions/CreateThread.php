<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Assistant\Models\AiAssistant;

class CreateThread
{
    public function __invoke(?AiAssistant $assistant = null): AiThread
    {
        $assistant ??= app(GetDefaultAiAssistant::class)(AiApplication::PersonalAssistant);

        $existingThread = auth()->user()->aiThreads()
            ->whereNull('name')
            ->whereBelongsTo($assistant, 'assistant')
            ->whereDoesntHave('messages')
            ->first();

        if ($existingThread) {
            return $existingThread;
        }

        $thread = new AiThread();
        $thread->assistant()->associate($assistant);
        $thread->user()->associate(auth()->user());

        $thread->assistant->model->getService()->createThread($thread);

        $thread->save();

        return $thread;
    }
}
