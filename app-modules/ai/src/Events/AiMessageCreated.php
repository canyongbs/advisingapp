<?php

namespace AdvisingApp\Ai\Events;

use AdvisingApp\Ai\Models\AiMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use AdvisingApp\Ai\Listeners\CreateAiMessageLog;

class AiMessageCreated
{
    use Dispatchable;
    use SerializesModels;

    public const LISTENERS = [
        CreateAiMessageLog::class,
    ];

    public function __construct(public AiMessage $aiMessage) {}
}
