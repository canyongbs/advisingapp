<?php

namespace Assist\IntegrationAI\Client;

use OpenAI;
use Closure;
use OpenAI\Client;
use OpenAI\Responses\StreamResponse;
use Assist\IntegrationAI\Settings\AISettings;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\IntegrationAI\DataTransferObjects\AIPrompt;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\DataTransferObjects\DynamicContext;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;

class AzureOpenAI implements AIChatClient
{
    private string $baseEndpoint;

    private string $apiKey;

    private string $apiVersion;

    private string $deployment;

    private ?string $dynamicContext = null;

    private ?string $systemContext = null;

    private Client $client;

    public function __construct(
    ) {
        $this->baseEndpoint = config('services.azure_open_ai.endpoint');
        $this->apiKey = config('services.azure_open_ai.api_key');
        $this->apiVersion = config('services.azure_open_ai.api_version');
        $this->deployment = config('services.azure_open_ai.deployment_name');

        $this->client = OpenAI::factory()
            ->withBaseUri("{$this->baseEndpoint}/openai/deployments/{$this->deployment}")
            ->withHttpHeader('api-key', $this->apiKey)
            ->withQueryParam('api-version', $this->apiVersion)
            ->make();
    }

    public function ask(Chat $chat, ?Closure $callback): string
    {
        if (is_null($this->systemContext)) {
            $this->setSystemContext();
        }

        $this->emitEvent($chat);

        /** @var StreamResponse $stream */
        $stream = $this->client->chat()->createStreamed([
            'messages' => $this->provideMessages($chat),
            'max_tokens' => resolve(AISettings::class)->max_tokens,
            'temperature' => resolve(AISettings::class)->temperature,
        ]);

        // TODO Determine if streamed response was successful

        $fullResponse = '';

        foreach ($stream as $response) {
            ray('response', $response);

            if (! is_null($callback)) {
                if ($streamedContent = $this->shouldSendResponse($response)) {
                    $callback($streamedContent);
                    $fullResponse .= $streamedContent;
                }
            }
        }

        return $fullResponse;
    }

    public function provideDynamicContext(DynamicContext $context): self
    {
        $this->setDynamicContext($context->context);

        return $this;
    }

    protected function setSystemContext(): void
    {
        $this->systemContext = resolve(AISettings::class)->prompt_context;
    }

    protected function setDynamicContext(string $context): void
    {
        $this->dynamicContext = $context;
    }

    protected function shouldSendResponse(CreateStreamedResponse $response): ?string
    {
        return $response->choices[0]->delta->content ?: null;
    }

    protected function formatMessagesFromChat(Chat $chat): array
    {
        return [
            ['role' => 'system', 'content' => $this->addContextToMessages()],
            ...collect($chat->messages)->map(function (array $message) {
                return [
                    'role' => $message['from'],
                    'content' => $message['message'],
                ];
            }),
        ];
    }

    protected function addContextToMessages(): string
    {
        return $this->systemContext . $this->dynamicContext;
    }

    protected function emitEvent(Chat $chat): void
    {
        AIPromptInitiated::dispatch(AIPrompt::from([
            'user' => auth()->user(),
            'request' => request(),
            'timestamp' => now(),
            'message' => $chat->messages->latest()->message,
            'metadata' => json_encode([
                'systemContext' => $this->systemContext,
            ]),
        ]));
    }
}
