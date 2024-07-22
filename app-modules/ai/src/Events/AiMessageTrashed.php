<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Listeners\AiMessageCascadeDeleteAiMessageFiles;
use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AiMessageTrashed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public const LISTENERS = [
        AiMessageCascadeDeleteAiMessageFiles::class,
    ];

    public function __construct(public AiMessage $aiMessage) {}
}
