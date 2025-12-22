<?php

namespace AdvisingApp\IntegrationOpenAi\Prism\ValueObjects\Messages;

use Prism\Prism\Concerns\HasProviderOptions;
use Prism\Prism\Contracts\Message;

class DeveloperMessage implements Message
{
    use HasProviderOptions;

    public function __construct(
        public readonly string $content,
    ) {}
}
