<?php

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiAssistant;

class ResetAiServiceIds
{
    public function __invoke(AiModel $model): void
    {
        AiAssistant::query()
            ->where('model', $model)
            ->update([
                'assistant_id' => null,
            ]);

        AiThread::query()
            ->whereRelation('assistant', 'model', $model)
            ->update([
                'thread_id' => null,
            ]);
    }
}
