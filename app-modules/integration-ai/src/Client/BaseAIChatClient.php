<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\IntegrationAI\Client;

use Closure;
use OpenAI\Client;
use Illuminate\Support\Arr;
use OpenAI\Testing\ClientFake;
use OpenAI\Responses\StreamResponse;
use Assist\IntegrationAI\Settings\AISettings;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\IntegrationAI\DataTransferObjects\AIPrompt;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Client\Concerns\InitializesClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\DataTransferObjects\DynamicContext;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

abstract class BaseAIChatClient implements AIChatClient
{
    use InitializesClient;

    protected string $baseEndpoint;

    protected string $apiKey;

    protected string $apiVersion;

    protected string $deployment;

    protected ?string $dynamicContext = null;

    protected ?string $systemContext = null;

    protected Client|ClientFake $client;

    public function __construct(
    ) {
        $this->initializeClient();
    }

    public function ask(Chat $chat, ?Closure $callback): string
    {
        if (is_null($this->systemContext)) {
            $this->setSystemContext();
        }

        $this->dispatchPromptInitiatedEvent($chat);

        /** @var StreamResponse $stream */
        $stream = $this->client->chat()->createStreamed([
            'messages' => $this->formatMessagesFromChat($chat),
        ]);

        return $this->generateStreamedResponse($stream, $callback);
    }

    public function provideDynamicContext(DynamicContext $context): self
    {
        $this->setDynamicContext($context->context);

        return $this;
    }

    protected function generateStreamedResponse(StreamResponse $stream, Closure $callback): string
    {
        $fullResponse = '';

        foreach ($stream as $response) {
            $streamedContent = $this->shouldSendResponse($response);

            if (! is_null($streamedContent)) {
                $callback($streamedContent);

                $fullResponse .= $streamedContent;
            }
        }

        return $fullResponse;
    }

    protected function setSystemContext(): void
    {
        $this->systemContext = resolve(AISettings::class)->prompt_system_context;
    }

    protected function setDynamicContext(string $context): void
    {
        $this->dynamicContext = $context;
    }

    protected function shouldSendResponse(CreateStreamedResponse $response): ?string
    {
        if ($response->choices[0]) {
            $this->examineFinishReason($response);
        }

        return $response->choices[0]?->delta?->content ?: null;
    }

    // TODO We can utilize the finishReason in order to flag audit records that might need attention
    protected function examineFinishReason(CreateStreamedResponse $response): void
    {
        match ($response->choices[0]->finishReason) {
            'length' => throw new TokensExceededException('Your response was not successfully generated due to the max_tokens parameter or token limit being exceeded.'),
            'content_filter' => throw new ContentFilterException('Your response was not successfully generated due to a flag from our content filters.'),
            default => null,
        };
    }

    protected function formatMessagesFromChat(Chat $chat): array
    {
        return [
            ['role' => 'system', 'content' => $this->addContextToMessages()],
            ...$chat->messages->toCollection()->map(function (ChatMessage $message) {
                return [
                    'role' => $message->from,
                    'content' => $message->message,
                ];
            }),
            ['role' => 'system', 'content' => 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.'],
        ];
    }

    protected function addContextToMessages(): string
    {
        return "{$this->systemContext} {$this->dynamicContext}";
    }

    protected function dispatchPromptInitiatedEvent(Chat $chat): void
    {
        AIPromptInitiated::dispatch(AIPrompt::from([
            'user' => auth()->user(),
            'request' => [
                'ip' => request()->ip(),
                'headers' => Arr::only(
                    request()->headers->all(),
                    ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                ),
            ],
            'timestamp' => now(),
            'message' => $chat->messages->last()->message,
            'metadata' => [
                'systemContext' => $this->systemContext,
            ],
        ]));
    }
}
