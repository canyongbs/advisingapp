<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\AiMessageCascadeForceDeletingAiMessageFiles;

class AiMessageForceDeleting
{
    use Dispatchable;

    public const LISTENERS = [
        AiMessageCascadeForceDeletingAiMessageFiles::class,
    ];

    public function __construct(public AiMessage $aiMessage) {}
}
