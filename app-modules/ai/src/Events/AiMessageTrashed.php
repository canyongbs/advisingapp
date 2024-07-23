<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\AiMessageCascadeDeleteAiMessageFiles;

class AiMessageTrashed
{
    use Dispatchable;
    use SerializesModels;

    public const LISTENERS = [
        AiMessageCascadeDeleteAiMessageFiles::class,
    ];

    public function __construct(public AiMessage $aiMessage) {}
}
