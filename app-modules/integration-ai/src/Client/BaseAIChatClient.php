<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\IntegrationAI\Client;

use Closure;
use OpenAI\Client;
use Illuminate\Support\Arr;
use OpenAI\Testing\ClientFake;
use OpenAI\Responses\StreamResponse;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use AdvisingApp\IntegrationAI\Settings\AISettings;
use AdvisingApp\IntegrationAI\Events\AIPromptInitiated;
use AdvisingApp\IntegrationAI\DataTransferObjects\AIPrompt;
use AdvisingApp\IntegrationAI\Client\Contracts\AIChatClient;
use AdvisingApp\IntegrationAI\Client\Concerns\InitializesClient;
use AdvisingApp\IntegrationAI\Exceptions\ContentFilterException;
use AdvisingApp\IntegrationAI\DataTransferObjects\DynamicContext;
use AdvisingApp\IntegrationAI\Exceptions\TokensExceededException;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

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
