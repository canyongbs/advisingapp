<?php

namespace Assist\IntegrationAI\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\IntegrationAI\DataTransferObjects\AIPrompt;

class AIPromptInitiated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public AIPrompt $prompt,
    ) {}
}
