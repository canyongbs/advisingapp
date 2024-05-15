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
use App\Models\User;
use Illuminate\Support\Arr;
use OpenAI\Testing\ClientFake;
use OpenAI\Responses\StreamResponse;
use AdvisingApp\Ai\Settings\AISettings;
use OpenAI\Responses\Threads\ThreadResponse;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;
use AdvisingApp\Assistant\Actions\GetAiAssistantFromID;
use AdvisingApp\IntegrationAI\Events\AIPromptInitiated;
use AdvisingApp\IntegrationAI\DataTransferObjects\AIPrompt;
use AdvisingApp\IntegrationAI\Client\Contracts\AiChatClient;
use OpenAI\Responses\Threads\Messages\ThreadMessageResponse;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use AdvisingApp\IntegrationAI\Client\Concerns\InitializesClient;
use AdvisingApp\IntegrationAI\Exceptions\ContentFilterException;
use OpenAI\Responses\Threads\Messages\ThreadMessageListResponse;
use AdvisingApp\IntegrationAI\DataTransferObjects\DynamicContext;
use AdvisingApp\IntegrationAI\Exceptions\TokensExceededException;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

abstract class BaseAIChatClient implements AiChatClient
{
    use InitializesClient;

    protected string $baseEndpoint;

    protected string $apiKey;

    protected string $apiVersion;

    protected string $deployment;

    protected ?string $dynamicContext = null;

    protected ?string $systemContext = null;

    public Client|ClientFake $client;

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
        $this->setDynamicContext($context->getContext());

        return $this;
    }

    public function getDynamicContext(): ?string
    {
        return $this->dynamicContext;
    }

    public function uploadFiles(array $files)
    {
        /** @var TemporaryUploadedFile $tempFile */
        $tempFile = $files[0]['file'];

        $fileResource = fopen($tempFile->temporaryUrl(), 'r');

        // This currently does not work due to a validation error,
        // "purpose contains invalid purpose"
        $response = $this->client->files()->upload([
            'purpose' => 'assistants',
            'file' => $fileResource,
        ]);

        // $response = Http::attach(
        //     'file',
        //     $fileResource,
        //     $files[0]['name'],
        //     ['Content-Type' => 'text/csv']
        // )
        //     ->withHeaders([
        //         'api-key' => config('services.azure_open_ai.api_key'),
        //         'OpenAI-Beta' => 'assistants=v1',
        //         'Accept' => '*/*',
        //     ])
        //     ->withQueryParameters([
        //         'api-version' => config('services.azure_open_ai.personal_assistant_api_version'),
        //     ])
        //     ->post('https://cgbs-ai.openai.azure.com/openai/files', [
        //         'purpose' => '',
        //     ]);

        // $api_key = config('services.azure_open_ai.api_key');
        // $api_version = config('services.azure_open_ai.personal_assistant_api_version');
        // $file = $fileResource; // Replace $fileResource with your actual file resource
        // $filename = $files[0]['name']; // Ensure $files[0]['name'] contains the filename

        // // Initialize cURL session
        // $ch = curl_init();

        // // Set cURL options
        // curl_setopt($ch, CURLOPT_URL, 'https://cgbs-ai.openai.azure.com/openai/files?api-version=' . $api_version);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POST, true);

        // // Prepare the file in a cURL-friendly format
        // $cfile = new CURLFile($file, 'text/csv', $filename);

        // // Set the POST fields
        // $postFields = [
        //     'purpose' => 'assistants',
        //     'file' => $tempFilePath,
        // ];

        // curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // // Set the headers
        // $headers = [
        //     'api-key: ' . $api_key,
        //     'OpenAI-Beta: assistants=v1',
        //     'Accept: */*',
        // ];

        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // // Execute the cURL session
        // $response = curl_exec($ch);

        // // Check for errors and handle them
        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // }

        // // Close cURL session
        // curl_close($ch);

        if (is_resource($fileResource)) {
            fclose($fileResource);
        }

        return $response;
    }

    public function createThread(): ThreadResponse
    {
        return $this->client->threads()->create([]);
    }

    public function createMessageInThread(Chat $chat, string $assistantId, array $fileIds = []): ThreadMessageResponse
    {
        $this->dispatchAssistantPromptInitiatedEvent($chat, $assistantId, $fileIds);

        return $this->client->threads()->messages()->create($chat->threadId, [
            'role' => 'user',
            'content' => $chat->messages->last()->message,
            'file_ids' => $fileIds,
        ]);
    }

    public function createRunForThread(string $threadId, string $assistantId): ThreadRunResponse
    {
        return $this->client->threads()->runs()->create(
            threadId: $threadId,
            parameters: [
                'assistant_id' => $assistantId,
                'instructions' => $this->getAssistantContext($assistantId),
            ],
        );
    }

    public function getRunForThread(string $threadId, string $runId): ThreadRunResponse
    {
        return $this->client->threads()->runs()->retrieve(
            threadId: $threadId,
            runId: $runId,
        );
    }

    public function getLatestAssistantMessageInThread(string $threadId): ThreadMessageListResponse
    {
        return $this->client->threads()->messages()->list($threadId, [
            'order' => 'desc',
            'limit' => 1,
        ]);
    }

    protected function getAssistantContext(string $assistantId): string
    {
        $baseInstructions = resolve(GetAiAssistantFromID::class)->get($assistantId)->instructions;

        /** @var User $user */
        $user = auth()->user();
        $this->provideDynamicContext(new DynamicContext($user));

        return "{$baseInstructions} {$this->dynamicContext}";
    }

    protected function getContext(): string
    {
        $context = $this->systemContext;

        if ($this->dynamicContext) {
            $context .= rtrim($this->dynamicContext, '.') . '.';
        }

        return "{$context} When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.";
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
            ['role' => 'system', 'content' => $this->getContext()],
            ...$chat->messages->toCollection()->map(function (ChatMessage $message) {
                return [
                    'role' => $message->from,
                    'content' => $message->message ?? '',
                    ...($message->name ? ['name' => $message->name] : []),
                    ...($message->functionCall ? ['function_call' => $message->functionCall] : []),
                ];
            }),
        ];
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
                'systemContext' => $this->getContext(),
            ],
        ]));
    }

    protected function dispatchAssistantPromptInitiatedEvent(Chat $chat, string $assistantId, array $fileIds = []): void
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
                'systemContext' => $this->getAssistantContext($assistantId),
                'file_ids' => $fileIds,
            ],
        ]));
    }
}
