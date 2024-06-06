<?php

namespace AdvisingApp\Ai\Actions;

use Illuminate\Support\Facades\Bus;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Jobs\ReInitializeAiAssistant;
use AdvisingApp\Ai\Jobs\ReInitializeAiAssistantThreads;

class ReInitializeAiServiceAssistant
{
    public function __invoke(AiAssistant $assistant): void
    {
        Bus::chain([
            app(ReInitializeAiAssistant::class, ['assistant' => $assistant]),
            Bus::batch([
                app(ReInitializeAiAssistantThreads::class, ['assistant' => $assistant]),
            ])
                ->name("Re-initialize AI assistant threads for assistant: {$assistant->id}")
                ->allowFailures(),
        ])->dispatch();
    }
}
