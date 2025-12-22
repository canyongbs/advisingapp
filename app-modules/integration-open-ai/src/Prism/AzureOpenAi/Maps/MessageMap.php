<?php

namespace AdvisingApp\IntegrationOpenAi\Prism\AzureOpenAi\Maps;

use AdvisingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use Prism\Prism\Contracts\Message;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap as BaseMessageMap;

class MessageMap extends BaseMessageMap
{
    protected function mapMessage(Message $message): void
    {
        if ($message instanceof DeveloperMessage) {
            $this->mapDeveloperMessage($message);

            return;
        }

        parent::mapMessage($message);
    }

    protected function mapDeveloperMessage(DeveloperMessage $message): void
    {
        $this->mappedMessages[] = [
            'role' => 'developer',
            'content' => $message->content,
        ];
    }
}
