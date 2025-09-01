<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\IntegrationOpenAi\Services;

use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Ai\Support\StreamingChunks\Finish;
use AdvisingApp\Ai\Support\StreamingChunks\Meta;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiResponsesService\Concerns\InteractsWithResearchRequests;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiResponsesService\Concerns\InteractsWithVectorStores;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use App\Models\User;
use Closure;
use Exception;
use Generator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Prism\Prism\Contracts\Message;
use Prism\Prism\Contracts\Schema;
use Prism\Prism\Enums\ChunkType;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Prism;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

abstract class BaseOpenAiResponsesService implements AiService
{
    use InteractsWithVectorStores;
    use InteractsWithResearchRequests;

    public const FORMATTING_INSTRUCTIONS = 'When you answer, it is crucial that you format your response using rich text in markdown format. Do not ever mention in your response that the answer is being formatted/rendered in markdown.';

    public function __construct(
        protected AiIntegrationsSettings $settings,
    ) {}

    abstract public function getApiKey(): string;

    public function getApiVersion(): string
    {
        return 'preview';
    }

    abstract public function getModel(): string;

    public function complete(string $prompt, string $content, bool $shouldTrack = true): string
    {
        $aiSettings = app(AiSettings::class);

        try {
            $response = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'truncation' => 'auto',
                ])
                ->withSystemPrompt($prompt)
                ->withPrompt($content)
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asText();
        } catch (PrismRateLimitedException $exception) {
            foreach ($exception->rateLimits as $rateLimit) {
                if ($rateLimit->resetsAt?->isFuture()) {
                    throw new MessageResponseException("Rate limit exceeded, retry at {$rateLimit->resetsAt}.");
                }
            }

            throw new MessageResponseException('Rate limit exceeded, please try again later.');
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to complete the prompt: [' . $exception->getMessage() . '].');
        }

        if ($shouldTrack) {
            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }

        return $response->text;
    }

    /**
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function stream(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = []): Closure
    {
        $aiSettings = app(AiSettings::class);

        try {
            $vectorStoreId = $this->getReadyVectorStoreId($files);

            $stream = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'instructions' => $prompt,
                    'truncation' => 'auto',
                    ...(filled($vectorStoreId) ? [
                        'tools' => [[
                            'type' => 'file_search',
                            'vector_store_ids' => [$vectorStoreId],
                        ]],
                    ] : []),
                    ...$options,
                ])
                ->withPrompt($content)
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asStream();

            return function () use ($shouldTrack, $stream): Generator {
                try {
                    foreach ($stream as $chunk) {
                        if (
                            ($chunk->chunkType === ChunkType::Meta) &&
                            filled($chunk->meta?->id)
                        ) {
                            yield json_encode(['type' => 'next_request_options', 'options' => base64_encode(json_encode(['previous_response_id' => $chunk->meta->id]))]);

                            continue;
                        }

                        if ($chunk->chunkType !== ChunkType::Text) {
                            continue;
                        }

                        yield json_encode(['type' => 'content', 'content' => base64_encode($chunk->text)]);

                        if ($chunk->finishReason === FinishReason::Length) {
                            yield json_encode(['type' => 'content', 'content' => base64_encode('...'), 'incomplete' => true]);
                        }

                        if ($chunk->finishReason === FinishReason::Error) {
                            yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                            report(new MessageResponseException('Stream not successful.'));
                        }
                    }
                } catch (PrismRateLimitedException $exception) {
                    foreach ($exception->rateLimits as $rateLimit) {
                        if ($rateLimit->resetsAt?->isFuture()) {
                            yield json_encode(['type' => 'rate_limited', 'message' => 'Heavy traffic, just a few more moments...', 'retry_after_seconds' => now()->diffInSeconds($rateLimit->resetsAt) + 1]);

                            return;
                        }
                    }

                    yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                    report(new MessageResponseException('Thread run was rate limited, but the system was unable to extract the number of retry seconds: [' . $exception->getMessage() . '].'));
                } catch (Throwable $exception) {
                    yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                    report($exception);
                }

                if ($shouldTrack) {
                    dispatch(new RecordTrackedEvent(
                        type: TrackedEventType::AiExchange,
                        occurredAt: now(),
                    ));
                }
            };
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to stream the response: [' . $exception->getMessage() . '].');
        }
    }

    /**
     * Stream method that yields plain text chunks instead of base64 encoded JSON
     * Yields arrays with 'type' => 'text'/'next_request_options' and corresponding content
     *
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     * @param ?array<Message> $messages
     */
    public function streamRaw(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = [], ?array $messages = null): Closure
    {
        $aiSettings = app(AiSettings::class);

        try {
            $vectorStoreId = $this->getReadyVectorStoreId($files);

            $request = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'instructions' => $prompt,
                    'truncation' => 'auto',
                    ...(filled($vectorStoreId) ? [
                        'tools' => [[
                            'type' => 'file_search',
                            'vector_store_ids' => [$vectorStoreId],
                        ]],
                    ] : []),
                    ...$options,
                ]);

            if (filled($messages)) {
                $request = $request->withMessages($messages);
            } else {
                $request = $request->withPrompt($content);
            }

            $stream = $request
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asStream();

            return function () use ($shouldTrack, $stream): Generator {
                try {
                    foreach ($stream as $chunk) {
                        if (
                            ($chunk->chunkType === ChunkType::Meta) &&
                            filled($chunk->meta?->id)
                        ) {
                            yield new Meta(
                                messageId: $chunk->meta->id,
                                nextRequestOptions: ['previous_response_id' => $chunk->meta->id],
                            );

                            continue;
                        }

                        if ($chunk->chunkType !== ChunkType::Text) {
                            continue;
                        }

                        yield new Text($chunk->text);

                        if ($chunk->finishReason) {
                            yield new Finish(
                                isIncomplete: $chunk->finishReason === FinishReason::Length,
                                error: ($chunk->finishReason === FinishReason::Error) ? 'Something went wrong' : null,
                            );

                            break;
                        }
                    }
                } catch (PrismRateLimitedException $exception) {
                    foreach ($exception->rateLimits as $rateLimit) {
                        if ($rateLimit->resetsAt?->isFuture()) {
                            yield new Finish(
                                rateLimitResetsAt: $rateLimit->resetsAt,
                            );

                            break;
                        }
                    }
                } catch (Throwable $exception) {
                    report($exception);

                    yield new Finish(
                        error: 'Something went wrong',
                    );
                }

                if ($shouldTrack) {
                    dispatch(new RecordTrackedEvent(
                        type: TrackedEventType::AiExchange,
                        occurredAt: now(),
                    ));
                }
            };
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to stream the response: [' . $exception->getMessage() . '].');
        }
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function sendMessage(AiMessage $message, array $files): Closure
    {
        return $this->sendNewMessage($message, $files);
    }

    public function getMessagePreviousResponseId(AiMessage $message): ?string
    {
        $previousResponseId = $message->thread->messages()
            ->whereDoesntHave('user')
            ->whereKeyNot($message)
            ->latest()
            ->first()
            ?->message_id;

        if (blank($previousResponseId)) {
            return null;
        }

        if (! str_starts_with($previousResponseId, 'resp_')) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $this->getApiKey(),
            ])
                ->withQueryParameters(['api-version' => $this->getApiVersion()])
                ->baseUrl($this->getDeployment())
                ->get("responses/{$previousResponseId}");

            return $response->ok() ? $previousResponseId : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function completeResponse(AiMessage $response): Closure
    {
        return $this->sendNewMessage($response, [], new UserMessage('Continue generating the response, do not mention that I told you as I will paste it directly after the last message.'));
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function retryMessage(AiMessage $message, array $files): Closure
    {
        return $this->sendNewMessage($message, $files);
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        $limit = 32768;

        $limit -= strlen(resolve(AiSettings::class)->prompt_system_context);
        $limit -= strlen(static::FORMATTING_INSTRUCTIONS);

        $limit -= 600; // For good measure.
        $limit -= ($limit % 100); // Round down to the nearest 100.

        return $limit;
    }

    /**
     * @param array<AiFile> $files
     */
    public function getReadyVectorStoreId(array $files): ?string
    {
        if (blank($files)) {
            return null;
        }

        return OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereMorphedTo('file', $files) /** @phpstan-ignore argument.type */
            ->whereNotNull('ready_until')
            ->where('ready_until', '>=', now())
            ->whereNotNull('vector_store_id')
            ->value('vector_store_id');
    }

    public function hasReasoning(): bool
    {
        return false;
    }

    public function hasTemperature(): bool
    {
        return true;
    }

    abstract public function getDeployment(): ?string;

    public function getDeploymentHash(): string
    {
        return md5($this->getDeployment());
    }

    /**
     * @param array<AiFile> $files
     */
    public function areFilesReady(array $files): bool
    {
        if (! $files) {
            return true;
        }

        return DB::transaction(function () use ($files): bool {
            $this->deleteExpiredVectorStoresForFiles($files);

            $vectorStores = $this->getValidExistingVectorStoresForFiles($files);

            $vectorStore = Arr::first($vectorStores);

            if (filled($vectorStore)) {
                $this->deleteOldFilesFromVectorStore($vectorStore, newFiles: $files);
            }

            if (blank($vectorStore)) {
                $this->createVectorStoreForFiles($files);

                return false;
            }

            $missingFiles = [];

            foreach ($files as $file) {
                foreach ($vectorStores as $vectorStore) {
                    if ($vectorStore->file()->is($file)) { /** @phpstan-ignore argument.type */
                        continue 2;
                    }
                }

                $missingFiles[] = $file;
            }

            $vectorStore = Arr::first($vectorStores);

            if (filled($missingFiles)) {
                $vectorStoreState = $this->getVectorStoreState($vectorStore);

                if (blank($vectorStoreState)) {
                    $this->createVectorStoreForFiles($files);

                    return false;
                }

                foreach ($missingFiles as $missingFile) {
                    $this->uploadMissingFileToVectorStore($vectorStore, $missingFile);
                }

                return false;
            }

            foreach ($vectorStores as $vectorStore) {
                if (! $vectorStore->ready_until?->isFuture()) {
                    $vectorStoreState = $this->getVectorStoreState($vectorStore);

                    return $this->evaluateVectorStoreState($vectorStore, $vectorStoreState);
                }
            }

            return true;
        });
    }

    /**
     * @param array<AiMessageFile> $files
     */
    protected function sendNewMessage(AiMessage $message, array $files, ?UserMessage $userMessage = null): Closure
    {
        $previousMessages = [];

        $previousResponseId = $this->getMessagePreviousResponseId($message);

        if (blank($previousResponseId)) {
            $previousMessages = $message->thread->messages()
                ->oldest()
                ->get()
                ->map(fn (AiMessage $message): Message => filled($message->user_id)
                    ? new UserMessage($message->content)
                    : new AssistantMessage($message->content))
                ->all();
        }

        $aiSettings = app(AiSettings::class);
        $instructions = $this->generateAssistantInstructions($message->thread->assistant, $message->thread->user, withDynamicContext: true);

        try {
            $vectorStoreIds = array_values(array_filter([
                $this->getReadyVectorStoreId([
                    ...$files,
                    ...AiMessageFile::query()
                        ->whereNotNull('parsing_results')
                        ->whereHas(
                            'message',
                            fn (Builder $query) => $query
                                ->whereKeyNot($message->getKey())
                                ->whereBelongsTo($message->thread, 'thread'),
                        )
                        ->get()
                        ->all(),
                ]),
                $this->getReadyVectorStoreId($message->thread->assistant->files()
                    ->whereNotNull('parsing_results')
                    ->get()
                    ->all()),
            ]));

            return $this->streamRaw(
                prompt: $instructions,
                content: $message->content,
                options: [
                    'previous_response_id' => $previousResponseId,
                    ...($this->hasReasoning() ? [
                        'reasoning' => [
                            'effort' => $aiSettings->reasoning_effort->value,
                        ],
                    ] : []),
                    ...(filled($vectorStoreIds) ? [
                        'tool_choice' => filled($files) ? [
                            'type' => 'file_search',
                        ] : 'auto',
                        'tools' => [[
                            'type' => 'file_search',
                            'vector_store_ids' => $vectorStoreIds,
                        ]],
                    ] : []),
                ],
                messages: [
                    ...$previousMessages,
                    $userMessage ?? new UserMessage($message->content),
                ],
            );
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to send a message: [' . $exception->getMessage() . '].');
        } finally {
            try {
                if (is_null($message->thread->name)) {
                    $prompt = $message->context . "\nThe following is the start of a chat between you and a user:\n" . $message->content;

                    $message->thread->name = $this->complete($prompt, 'Generate a title for this chat, in 5 words or less. Do not respond with any greetings or salutations, and do not include any additional information or context. Just respond with the title:');

                    $message->thread->saved_at = now();

                    $message->thread->save();

                    dispatch(new RecordTrackedEvent(
                        type: TrackedEventType::AiThreadSaved,
                        occurredAt: now(),
                    ));
                }
            } catch (Exception $exception) {
                report($exception);

                $message->thread->name = 'Untitled Chat';
            }

            $message->context = $instructions;
            $message->save();

            $message->files()->saveMany($files);
        }
    }

    /**
     * @param array<string, mixed> $providerOptions
     *
     * @return array<mixed>
     */
    protected function structured(string $prompt, string $content, Schema $schema, array $providerOptions = [], ?string &$responseId = null): array
    {
        $aiSettings = app(AiSettings::class);

        try {
            $response = Prism::structured()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'schema' => [
                        'strict' => true,
                    ],
                    'truncation' => 'auto',
                    ...$providerOptions,
                ])
                ->withSystemPrompt($prompt)
                ->withPrompt($content)
                ->withSchema($schema)
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asStructured();
        } catch (PrismRateLimitedException $exception) {
            foreach ($exception->rateLimits as $rateLimit) {
                if ($rateLimit->resetsAt?->isFuture()) {
                    throw new MessageResponseException("Rate limit exceeded, retry at {$rateLimit->resetsAt}.");
                }
            }

            throw new MessageResponseException('Rate limit exceeded, please try again later.');
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to complete the prompt: [' . $exception->getMessage() . '].');
        }

        if (filled($response->meta->id)) {
            $responseId = $response->meta->id;
        }

        return $response->structured;
    }

    protected function generateAssistantInstructions(AiAssistant $assistant, User $user, bool $withDynamicContext = false): string
    {
        $assistantInstructions = rtrim($assistant->instructions, '. ');

        $maxAssistantInstructionsLength = $this->getMaxAssistantInstructionsLength();

        if (strlen($assistantInstructions) > $maxAssistantInstructionsLength) {
            $truncationEnd = '... [truncated]';

            $assistantInstructions = (string) str($assistantInstructions)
                ->limit($maxAssistantInstructionsLength - strlen($truncationEnd), $truncationEnd);
        }

        $formattingInstructions = static::FORMATTING_INSTRUCTIONS;

        if ($withDynamicContext) {
            $dynamicContext = rtrim($user->getDynamicContext(), '. ');

            return "{$dynamicContext}.\n\n{$assistantInstructions}.\n\n{$formattingInstructions}";
        }

        return "{$assistantInstructions}.\n\n{$formattingInstructions}";
    }
}
