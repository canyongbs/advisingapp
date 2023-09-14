<?php

namespace Assist\IntegrationAI\Client\Playground;

use Closure;
use OpenAI\Testing\ClientFake;
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
    private ?string $dynamicContext = null;

    private ?string $systemContext = null;

    private $handle;

    private ClientFake $client;

    public function __construct(
    ) {
        $fakeText = resolve(MockStreamResponseGenerator::class)->generateFakeStreamResponse();

        $this->handle = fopen('php://memory', 'r+');
        fwrite($this->handle, $fakeText);
        rewind($this->handle);

        $this->client = new ClientFake([
            CreateStreamedResponse::fake(fread($this->handle, strlen($fakeText))),
        ]);
    }

    public function ask(Chat $chat, ?Closure $callback): string
    {
        if (is_null($this->systemContext)) {
            $this->setSystemContext();
        }

        /** @var StreamResponse $stream */
        $stream = $this->client->chat()->createStreamed([
            'messages' => $this->formatMessagesFromChat($chat),
        ]);

        $fullResponse = '';

        // TODO We can probably extract some of this into pieces that both the playground and the real instance share.
        // The core difference between the real implementation and the playground is simply the connection/client itself
        foreach ($stream as $response) {
            sleep(1);

            $streamedContent = $this->shouldSendResponse($response);

            if (! is_null($callback)) {
                if (! is_null($streamedContent)) {
                    $callback($streamedContent);
                }
            }

            $fullResponse .= $streamedContent;
        }

        fclose($this->handle);

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
        return $this->systemContext . ' ' . $this->dynamicContext;
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
