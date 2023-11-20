<?php

namespace Assist\IntegrationAI\Client\Contracts;

use Closure;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;

interface AIChatClient
{
    public function ask(Chat $chat, Closure $callback): string;
}
