<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Ai\Support\StreamingChunks\Image;
use AdvisingApp\Ai\Support\StreamingChunks\Meta;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AdvisingApp\IntegrationOpenAi\Prism\ValueObjects\Messages\DeveloperMessage;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService\Concerns\InteractsWithResearchRequests;
use AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService\Concerns\InteractsWithVectorStores;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use App\Models\User;
use Closure;
use Exception;
use Generator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Contracts\Message;
use Prism\Prism\Contracts\Schema;
use Prism\Prism\Enums\ChunkType;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Prism;
use Prism\Prism\Text\PendingRequest;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Throwable;

abstract class BaseOpenAiService implements AiService
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
                ->withMaxTokens(null)
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

    public function image(string $prompt): string
    {
        try {
            $response = Prism::image()
                ->using('azure_open_ai', $this->getImageGenerationDeployment())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'output_format' => 'jpeg',
                ])
                ->withPrompt($prompt)
                ->generate();
        } catch (PrismRateLimitedException $exception) {
            foreach ($exception->rateLimits as $rateLimit) {
                if ($rateLimit->resetsAt?->isFuture()) {
                    throw new MessageResponseException("Rate limit exceeded, retry at {$rateLimit->resetsAt}.");
                }
            }

            throw new MessageResponseException('Rate limit exceeded, please try again later.');
        } catch (Throwable $exception) {
            report($exception);

            throw new MessageResponseException('Failed to generate an image: [' . $exception->getMessage() . '].');
        }

        $image = $response->firstImage();

        if (! $image->hasBase64()) {
            throw new MessageResponseException('Failed to generate an image: [No image was returned].');
        }

        return $image->base64;
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
                ])
                ->withPrompt($content)
                ->withMaxTokens(null)
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null);

            return function () use ($shouldTrack, $request): Generator {
                // We will create a function to handle the request here, then call it within a retry loop below so that we can retry the request in case of specific errors
                $handleRequest = function (PendingRequest $request) {
                    try {
                        $clonedRequest = clone $request;

                        $stream = $clonedRequest->asStream();

                        foreach ($stream as $chunk) {
                            if (
                                ($chunk->chunkType === ChunkType::Meta) &&
                                filled($chunk->meta?->id)
                            ) {
                                yield json_encode(['type' => 'next_request_options', 'options' => base64_encode(json_encode(['previous_response_id' => $chunk->meta->id]))]);

                                continue;
                            }

                            if ($chunk->chunkType !== ChunkType::Text) {
                                Log::info('Received unhandled AI stream chunk.', [
                                    'chunk' => $chunk,
                                ]);

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
                        // Throw to bubble up to the retry handling
                        throw $exception;
                    }
                };

                $maxRetries = 3;
                $retryDelays = [1, 3, 5];

                for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
                    try {
                        yield from $handleRequest($request);

                        break;
                    } catch (Throwable $exception) {
                        // Check if this is an exception we should retry for
                        $shouldRetry = $exception instanceof PrismException && str_contains($exception->getMessage(), 'server_error');

                        if (! $shouldRetry || $attempt >= $maxRetries - 1) {
                            yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                            report($exception);
                        }

                        sleep($retryDelays[$attempt]);
                    }
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
    public function streamRaw(?string $prompt = null, ?string $content = null, array $files = [], bool $shouldTrack = true, array $options = [], ?array $messages = null, bool $hasImageGeneration = false): Closure
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
                    ...($hasImageGeneration ? ['headers' => ['x-ms-oai-image-generation-deployment' => $this->getImageGenerationDeployment()]] : []),
                ])
                ->withProviderOptions([
                    ...(filled($prompt) ? ['instructions' => $prompt] : []),
                    'truncation' => 'auto',
                    ...((filled($vectorStoreId) || $hasImageGeneration) ? [
                        'tools' => [
                            ...filled($vectorStoreId) ? [[
                                'type' => 'file_search',
                                'vector_store_ids' => [$vectorStoreId],
                            ]] : [],
                            ...$hasImageGeneration ? [[
                                'type' => 'image_generation',
                            ]] : [],
                        ],
                    ] : []),
                    ...$options,
                ]);

            if (filled($messages)) {
                $request->withMessages($messages);
            } elseif (filled($content)) {
                $request->withPrompt($content);
            }

            $request
                ->withMaxTokens(null)
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null);

            if ($hasImageGeneration) {
                return function () use ($shouldTrack, $request): Generator {
                    // We will create a function to handle the request here, then call it within a retry loop below so that we can retry the request in case of specific errors
                    $handleRequest = function (PendingRequest $request) {
                        try {
                            $clonedRequest = clone $request;

                            $response = $clonedRequest->asText();

                            if (filled($response->meta->id)) {
                                yield new Meta(
                                    messageId: $response->meta->id,
                                    nextRequestOptions: ['previous_response_id' => $response->meta->id],
                                );
                            }

                            if (filled($response->text)) {
                                yield new Text(
                                    filled($response->additionalContent['generated_images'] ?? [])
                                        ? $response->text
                                        : ('We have determined that an image is not needed for this request.' . PHP_EOL . PHP_EOL . $response->text),
                                );
                            }

                            if (filled($response->additionalContent['generated_images'] ?? [])) {
                                foreach ($response->additionalContent['generated_images'] as $image) {
                                    yield new Image(
                                        content: $image['result'],
                                        format: $image['output_format'],
                                    );
                                }
                            }

                            yield new Finish(
                                isIncomplete: $response->finishReason === FinishReason::Length,
                                error: ($response->finishReason === FinishReason::Error) ? 'Something went wrong' : null,
                            );
                        } catch (PrismRateLimitedException $exception) {
                            foreach ($exception->rateLimits as $rateLimit) {
                                if ($rateLimit->resetsAt?->isFuture()) {
                                    yield new Finish(
                                        rateLimitResetsAt: $rateLimit->resetsAt,
                                    );

                                    break;
                                }
                            }
                        } catch (PrismException $exception) {
                            // Throw to pass up to the caller
                            throw $exception;
                        } catch (Throwable $exception) {
                            report($exception);

                            yield new Finish(
                                error: 'Something went wrong',
                            );
                        }
                    };

                    $maxRetries = 3;
                    $retryDelays = [1, 3, 5];

                    for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
                        try {
                            yield from $handleRequest($request);

                            break;
                        } catch (Throwable $exception) {
                            // Check if this is an exception we should retry for
                            $shouldRetry = $exception instanceof PrismException && str_contains($exception->getMessage(), 'server_error');

                            if (! $shouldRetry || $attempt >= $maxRetries - 1) {
                                report($exception);

                                yield new Finish(
                                    error: 'Something went wrong',
                                );
                            }

                            sleep($retryDelays[$attempt]);
                        }
                    }

                    if ($shouldTrack) {
                        dispatch(new RecordTrackedEvent(
                            type: TrackedEventType::AiExchange,
                            occurredAt: now(),
                        ));
                    }
                };
            }

            return function () use ($shouldTrack, $request): Generator {
                // We will create a function to handle the request here, then call it within a retry loop below so that we can retry the request in case of specific errors
                $handleRequest = function (PendingRequest $request) {
                    try {
                        $clonedRequest = clone $request;

                        $stream = $clonedRequest->asStream();

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
                                Log::info('Received unhandled AI stream chunk.', [
                                    'chunk' => $chunk,
                                ]);

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
                    } catch (PrismException $exception) {
                        // Throw to pass up to the caller
                        throw $exception;
                    } catch (Throwable $exception) {
                        report($exception);

                        yield new Finish(
                            error: 'Something went wrong',
                        );
                    }
                };

                $maxRetries = 3;
                $retryDelays = [1, 3, 5];

                for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
                    try {
                        yield from $handleRequest($request);

                        break;
                    } catch (Throwable $exception) {
                        // Check if this is an exception we should retry for
                        $shouldRetry = $exception instanceof PrismException && str_contains($exception->getMessage(), 'server_error');

                        if (! $shouldRetry || $attempt >= $maxRetries - 1) {
                            report($exception);

                            yield new Finish(
                                error: 'Something went wrong',
                            );
                        }

                        sleep($retryDelays[$attempt]);
                    }
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

    public function hasImageGeneration(): bool
    {
        return filled($this->getImageGenerationDeployment());
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function sendMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        return $this->sendNewMessage($message, $files, $hasImageGeneration);
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
                ->baseUrl("{$this->getDeployment()}/v1")
                ->get("responses/{$previousResponseId}");

            return $response->ok() ? $previousResponseId : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function completeResponse(AiMessage $response): Closure
    {
        return $this->sendNewMessage($response, [], userMessage: new UserMessage('Continue generating the response, do not mention that I told you as I will paste it directly after the last message.'));
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function retryMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        return $this->sendNewMessage($message, $files, $hasImageGeneration);
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
    public function getReadyVectorStoreId(array $files, ?Model $context = null): ?string
    {
        if (blank($files)) {
            return null;
        }

        $query = OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereMorphedTo('file', $files) /** @phpstan-ignore argument.type */
            ->whereNotNull('ready_until')
            ->where('ready_until', '>=', now())
            ->whereNotNull('vector_store_id');

        $this->scopeVectorStoreQueryByContext($query, $context);

        return $query->value('vector_store_id');
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
    public function areFilesReady(array $files, ?Model $context = null): bool
    {
        if (! $files) {
            return true;
        }

        $this->deleteExpiredVectorStoresForFiles($files, $context);

        $vectorStores = $this->getValidExistingVectorStoresForFiles($files, $context);
        $primaryVectorStore = Arr::first($vectorStores);

        if (filled($primaryVectorStore)) {
            $this->deleteOldFilesFromVectorStore($primaryVectorStore, newFiles: $files, context: $context);
        }

        if (blank($primaryVectorStore)) {
            $this->createVectorStoreForFiles($files, $context);

            return false;
        }

        $missingFiles = [];
        $outdatedFiles = [];

        foreach ($files as $file) {
            $matchingVectorStore = null;

            foreach ($vectorStores as $existingVectorStore) {
                if ($existingVectorStore->file()->is($file)) { /** @phpstan-ignore argument.type */
                    $matchingVectorStore = $existingVectorStore;

                    break;
                }
            }

            if (blank($matchingVectorStore)) {
                $missingFiles[] = $file;

                continue;
            }

            if (
                $file instanceof Model
                && filled($file->getAttributeValue('updated_at'))
                && $matchingVectorStore->getAttributeValue('updated_at')
                && $file->getAttributeValue('updated_at')->isAfter($matchingVectorStore->getAttributeValue('updated_at'))
            ) {
                $outdatedFiles[] = [
                    'file' => $file,
                    'vectorStore' => $matchingVectorStore,
                ];
            }
        }

        if (filled($missingFiles) || filled($outdatedFiles)) {
            $vectorStoreState = $this->getVectorStoreState($primaryVectorStore);

            if (blank($vectorStoreState)) {
                $this->createVectorStoreForFiles($files, $context);

                return false;
            }

            foreach ($missingFiles as $missingFile) {
                $this->uploadMissingFileToVectorStore($primaryVectorStore, $missingFile, $context);
            }

            foreach ($outdatedFiles as $outdatedFileData) {
                /** @var OpenAiVectorStore $outdatedVectorStore */
                $outdatedVectorStore = $outdatedFileData['vectorStore'];
                $this->updateFileInVectorStore($primaryVectorStore, $outdatedVectorStore, $outdatedFileData['file'], $context);
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
    }

    public function getImageGenerationDeployment(): ?string
    {
        return null;
    }

    /**
     * @param array<AiMessageFile> $files
     */
    protected function sendNewMessage(AiMessage $message, array $files, bool $hasImageGeneration = false, ?UserMessage $userMessage = null): Closure
    {
        if ($hasImageGeneration && (! $this->hasImageGeneration())) {
            $hasImageGeneration = false;
        }

        $instructions = $this->generateAssistantInstructions($message->thread->assistant, $message->thread->user, withDynamicContext: true);

        $previousMessages = [];

        $previousResponseId = $this->getMessagePreviousResponseId($message);

        if (blank($previousResponseId)) {
            $previousMessages = [
                ...($this->hasReasoning() ? [new DeveloperMessage($instructions)] : []),
                ...$message->thread->messages()
                    ->oldest()
                    ->get()
                    ->map(fn (AiMessage $message): Message => filled($message->user_id)
                        ? new UserMessage($message->content)
                        : new AssistantMessage($message->content))
                    ->all(),
            ];
        }

        $aiSettings = app(AiSettings::class);

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
                $this->getReadyVectorStoreId([
                    ...$message->thread->assistant->files()
                        ->whereNotNull('parsing_results')
                        ->get()
                        ->all(),
                    ...$message->thread->assistant->links()
                        ->whereNotNull('parsing_results')
                        ->get()
                        ->all(),
                    ...$message->thread->assistant->getResourceHubArticles(),
                ], $message->thread->assistant),
            ]));

            return $this->streamRaw(
                prompt: $this->hasReasoning() ? null : $instructions,
                options: [
                    'previous_response_id' => $previousResponseId,
                    ...($this->hasReasoning() ? [
                        'reasoning' => [
                            'effort' => $aiSettings->reasoning_effort->value,
                        ],
                    ] : []),
                    ...((filled($vectorStoreIds) || $hasImageGeneration) ? [
                        'tools' => [
                            ...filled($vectorStoreIds) ? [[
                                'type' => 'file_search',
                                'vector_store_ids' => $vectorStoreIds,
                            ]] : [],
                            ...$hasImageGeneration ? [[
                                'type' => 'image_generation',
                                'output_format' => 'jpeg',
                            ]] : [],
                        ],
                    ] : []),
                ],
                messages: [
                    ...$previousMessages,
                    $userMessage ?? new UserMessage($message->content),
                ],
                hasImageGeneration: $hasImageGeneration,
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
                ->withMaxTokens(null)
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
