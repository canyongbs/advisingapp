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
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Assistants\AssistantsDataTransferObject;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Threads\ThreadsDataTransferObject;
use AdvisingApp\IntegrationOpenAi\Exceptions\FileUploadsCannotBeDisabled;
use AdvisingApp\IntegrationOpenAi\Exceptions\FileUploadsCannotBeEnabled;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiResearchRequestVectorStore;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Research\Models\ResearchRequest;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use Generator;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
    use HasAiServiceHelpers;

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
     * @param array<string, mixed> $options
     *
     * @return array{response: array<mixed>, nextRequestOptions: array<string, mixed>}
     */
    public function structuredResearchRequestRequest(ResearchRequest $researchRequest, string $prompt, string $content, Schema $schema, array $options = []): array
    {
        $responseId = null;

        $response = $this->structured(
            prompt: $prompt,
            content: $content,
            schema: $schema,
            providerOptions: [
                'tool_choice' => [
                    'type' => 'file_search',
                ],
                'tools' => [[
                    'type' => 'file_search',
                    'vector_store_ids' => $this->getReadyResearchRequestVectorStoreIds($researchRequest),
                ]],
                ...$options,
            ],
            responseId: $responseId,
        );

        return [
            'response' => $response,
            'nextRequestOptions' => filled($responseId) ? [
                'previous_response_id' => $responseId,
            ] : [],
        ];
    }

    /**
     * @return array<string>
     */
    public function getReadyResearchRequestVectorStoreIds(ResearchRequest $researchRequest): array
    {
        return OpenAiResearchRequestVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereBelongsTo($researchRequest)
            ->whereNotNull('ready_until')
            ->where('ready_until', '>=', now())
            ->pluck('vector_store_id')
            ->all();
    }

    /**
     * @return array<mixed>
     */
    public function structured(string $prompt, string $content, Schema $schema, array $providerOptions = [], ?string &$responseId = null): array
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
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
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

        dispatch(new RecordTrackedEvent(
            type: TrackedEventType::AiExchange,
            occurredAt: now(),
        ));

        $responseId = $response->meta->id;

        return $response->structured;
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function sendMessage(AiMessage $message, array $files, Closure $saveResponse, ?UserMessage $userMessage = null): Closure
    {
        $previousMessages = [];

        $previousResponseId = $this->getMessagePreviousResponseId($message);

        if (blank(value: $previousResponseId)) {
            $previousMessages = $message->thread->messages()
                ->oldest()
                ->get()
                ->map(fn (AiMessage $message): Message => filled($message->user_id)
                    ? new UserMessage($message->content)
                    : new AssistantMessage($message->content))
                ->all();
        }

        $aiSettings = app(AiSettings::class);
        $instructions = $this->generateAssistantInstructions($message->thread->assistant, withDynamicContext: true);

        try {
            $vectorStoreIds = $this->getReadyVectorStoreIds([
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
                ...$message->thread->assistant->files()
                    ->whereNotNull('parsing_results')
                    ->get()
                    ->all(),
            ]);

            $stream = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'previous_response_id' => $previousResponseId,
                    ...($this->hasReasoning() ? [
                        'reasoning' => [
                            'effort' => $aiSettings->reasoning_effort->value,
                        ],
                    ] : []),
                    'truncation' => 'auto',
                    ...(filled($vectorStoreIds) ? [
                        'tool_choice' => filled($files) ? [
                            'type' => 'file_search',
                        ] : 'auto',
                        'tools' => [[
                            'type' => 'file_search',
                            'vector_store_ids' => $vectorStoreIds,
                        ]],
                    ] : []),
                ])
                ->withSystemPrompt($instructions)
                ->withMessages([
                    ...$previousMessages,
                    $userMessage ?? new UserMessage($message->content),
                ])
                ->withMaxTokens($aiSettings->max_tokens->getTokens())
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asStream();

            return $this->streamResponse($stream, $message, $saveResponse);
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

            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }
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

    public function completeResponse(AiMessage $response, Closure $saveResponse): Closure
    {
        return $this->sendMessage($response, [], $saveResponse, new UserMessage('Continue generating the response, do not mention that I told you as I will paste it directly after the last message.'));
    }

    /**
     * @param array<AiMessageFile> $files
     */
    public function retryMessage(AiMessage $message, array $files, Closure $saveResponse): Closure
    {
        return $this->sendMessage($message, $files, $saveResponse);
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

    public function createAssistant(AiAssistant $assistant): void {}

    public function updateAssistant(AiAssistant $assistant): void {}

    public function retrieveAssistant(AiAssistant $assistant): ?AssistantsDataTransferObject
    {
        return null;
    }

    /**
     * @param array<string> $tools
     */
    public function updateAssistantTools(AiAssistant $assistant, array $tools): void {}

    public function enableAssistantFileUploads(AiAssistant $assistant): void
    {
        throw new FileUploadsCannotBeEnabled();
    }

    public function disableAssistantFileUploads(AiAssistant $assistant): void
    {
        throw new FileUploadsCannotBeDisabled();
    }

    public function createThread(AiThread $thread): void {}

    public function retrieveThread(AiThread $thread): ?ThreadsDataTransferObject
    {
        return null;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function modifyThread(AiThread $thread, array $parameters): ?ThreadsDataTransferObject
    {
        return null;
    }

    public function deleteThread(AiThread $thread): void {}

    public function isAssistantExisting(AiAssistant $assistant): bool
    {
        return true;
    }

    public function isThreadExisting(AiThread $thread): bool
    {
        return true;
    }

    public function supportsMessageFileUploads(): bool
    {
        return true;
    }

    public function supportsAssistantFileUploads(): bool
    {
        return true;
    }

    public function isFileReady(AiFile $file): bool
    {
        $vectorStore = $this->findOrCreateVectorStoreRecordForFile($file);

        if ($vectorStore->ready_until?->isFuture()) {
            return true;
        }

        if (($isFileReady = $this->isFileReadyInExistingVectorStore($file, $vectorStore)) !== null) {
            return $isFileReady;
        }

        $this->resetMissingVectorStoreFileId($vectorStore);

        if (($isFileReady = $this->uploadNewFileForVectorStore($file, $vectorStore)) !== null) {
            return $isFileReady;
        }

        $this->createVectorStoreForFile($file, $vectorStore);

        return false;
    }

    /**
     * @param array<AiFile> $files
     *
     * @return array<string>
     */
    public function getReadyVectorStoreIds(array $files): array
    {
        if (blank($files)) {
            return [];
        }

        return OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereMorphedTo('file', $files) /** @phpstan-ignore argument.type */
            ->whereNotNull('ready_until')
            ->where('ready_until', '>=', now())
            ->pluck('vector_store_id')
            ->all();
    }

    public function hasReasoning(): bool
    {
        return false;
    }

    public function hasTemperature(): bool
    {
        return true;
    }

    public function getDeploymentHash(): string
    {
        return md5($this->getDeployment());
    }

    public function isResearchRequestReady(ResearchRequest $researchRequest): bool
    {
        $vectorStore = $this->findOrCreateVectorStoreRecordForResearchRequest($researchRequest);

        if ($vectorStore->ready_until?->isFuture()) {
            return true;
        }

        if (($isResearchRequestReady = $this->isResearchRequestReadyInExistingVectorStore($researchRequest, $vectorStore)) !== null) {
            return $isResearchRequestReady;
        }

        $this->createVectorStoreForResearchRequest($researchRequest, $vectorStore);

        return false;
    }

    public function afterResearchRequestSearchQueriesParsed(ResearchRequest $researchRequest): void
    {
        DB::transaction(function () use ($researchRequest) {
            $vectorStore = $this->findOrCreateVectorStoreRecordForResearchRequest($researchRequest);
            $vectorStore->ready_until = null;
            $vectorStore->save();

            $fileIds = $this->uploadResearchRequestParsedSearchResultFilesForVectorStore($researchRequest, $vectorStore);

            if (blank($fileIds)) {
                return;
            }

            $this->attachResearchRequestFilesToVectorStore($fileIds, $vectorStore);
        });
    }

    protected function findOrCreateVectorStoreRecordForResearchRequest(ResearchRequest $researchRequest): OpenAiResearchRequestVectorStore
    {
        $deploymentHash = $this->getDeploymentHash();

        $vectorStore = OpenAiResearchRequestVectorStore::query()
            ->whereBelongsTo($researchRequest)
            ->where('deployment_hash', $deploymentHash)
            ->first();

        if ($vectorStore) {
            return $vectorStore;
        }

        $vectorStore = new OpenAiResearchRequestVectorStore();
        $vectorStore->researchRequest()->associate($researchRequest);
        $vectorStore->deployment_hash = $deploymentHash;

        return $vectorStore;
    }

    protected function isResearchRequestReadyInExistingVectorStore(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): ?bool
    {
        if (blank($vectorStore->vector_store_id)) {
            return null;
        }

        $getVectorStoreResponse = $this->vectorStoresHttpClient()
            ->get("vector_stores/{$vectorStore->vector_store_id}");

        $hasVectorStoreCompletedAllFiles = $getVectorStoreResponse->successful()
            && ($getVectorStoreResponse->json('status') === 'completed')
            && $getVectorStoreResponse->json('file_counts.completed')
            && ($getVectorStoreResponse->json('file_counts.completed') === $getVectorStoreResponse->json('file_counts.total'));

        $isVectorStoreProcessingFiles = $getVectorStoreResponse->successful()
            && ($getVectorStoreResponse->json('status') === 'in_progress')
            && $getVectorStoreResponse->json('file_counts.in_progress');

        if ((! $hasVectorStoreCompletedAllFiles) && $isVectorStoreProcessingFiles) {
            return false;
        } elseif (
            $hasVectorStoreCompletedAllFiles
            && $getVectorStoreResponse->json('expires_at')
            && (($vectorStoreExpiresAt = CarbonImmutable::createFromTimestampUTC($getVectorStoreResponse->json('expires_at')))->diffInHours() < -3)
        ) {
            $vectorStore->ready_until = $vectorStoreExpiresAt->subHours(2);
            $this->deleteExistingResearchRequestVectorStoreFiles($researchRequest, $vectorStore);
            $vectorStore->save();

            return true;
        }

        $vectorStore->vector_store_id = null;

        return null;
    }

    protected function deleteExistingResearchRequestVectorStoreFiles(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): void
    {
        $listFilesResponse = $this->vectorStoresHttpClient()
            ->get("vector_stores/{$vectorStore->vector_store_id}/files");

        if ((! $listFilesResponse->successful()) || ! is_array($listFilesResponse->json('data'))) {
            report(new Exception('Failed to list files for vector store [' . $vectorStore->vector_store_id . '] for research request [' . $researchRequest->getKey() . '], as a [' . $listFilesResponse->status() . '] response was returned: [' . $listFilesResponse->body() . '].'));

            return;
        }

        foreach (Arr::pluck($listFilesResponse->json('data'), 'id') as $fileId) {
            $deleteFileResponse = $this->filesHttpClient()
                ->delete("files/{$fileId}");

            if (! $deleteFileResponse->successful()) {
                report(new Exception('Failed to delete file [' . $fileId . '] associated with vector store [' . $vectorStore->vector_store_id . '] for research request [' . $researchRequest->getKey() . '], as a [' . $deleteFileResponse->status() . '] response was returned: [' . $deleteFileResponse->body() . '].'));
            }
        }
    }

    protected function createVectorStoreForResearchRequest(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): void
    {
        $fileIds = $this->uploadResearchRequestFilesForVectorStore($researchRequest, $vectorStore);

        if (blank($fileIds)) {
            return;
        }

        $createVectorStoreResponse = $this->vectorStoresHttpClient()
            ->acceptJson()
            ->asJson()
            ->post('vector_stores', [
                'name' => Str::limit($researchRequest->topic, 100),
                'file_ids' => $fileIds,
                'expires_after' => [
                    'anchor' => 'last_active_at',
                    'days' => 1,
                ],
            ]);

        if ((! $createVectorStoreResponse->successful()) || blank($createVectorStoreResponse->json('id'))) {
            report(new Exception('Failed to create vector store for research request [' . $researchRequest->getKey() . '], as a [' . $createVectorStoreResponse->status() . '] response was returned: [' . $createVectorStoreResponse->body() . '].'));

            $vectorStore->save();

            return;
        }

        $vectorStore->vector_store_id = $createVectorStoreResponse->json('id');
        $vectorStore->save();
    }

    /**
     * @return ?array<string>
     */
    protected function uploadResearchRequestFilesForVectorStore(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): ?array
    {
        $fileIds = [];

        foreach ($researchRequest->parsedFiles as $file) {
            $createFileResponse = $this->filesHttpClient()
                ->attach('file', $file->results, "{$file->media->name}.md", ['Content-Type' => 'text/markdown'])
                ->post('files', [
                    'purpose' => 'assistants',
                ]);

            if ((! $createFileResponse->successful()) || blank($createFileResponse->json('id'))) {
                report(new Exception('Failed to create research request file [' . $file->getKey() . '] for vector store, as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

                continue;
            }

            $fileIds[] = $createFileResponse->json('id');
        }

        foreach ($researchRequest->parsedLinks as $link) {
            $createFileResponse = $this->filesHttpClient()
                ->attach('file', $link->results, Str::limit($link->url, 100) . '.md', ['Content-Type' => 'text/markdown'])
                ->post('files', [
                    'purpose' => 'assistants',
                ]);

            if ((! $createFileResponse->successful()) || blank($createFileResponse->json('id'))) {
                report(new Exception('Failed to create research request link file [' . $link->getKey() . '] for vector store, as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

                continue;
            }

            $fileIds[] = $createFileResponse->json('id');
        }

        return [
            ...$fileIds,
            ...$this->uploadResearchRequestParsedSearchResultFilesForVectorStore($researchRequest, $vectorStore),
        ];
    }

    /**
     * @return ?array<string>
     */
    protected function uploadResearchRequestParsedSearchResultFilesForVectorStore(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): ?array
    {
        $fileIds = [];

        foreach ($researchRequest->parsedSearchResults as $searchResult) {
            $createFileResponse = $this->filesHttpClient()
                ->attach('file', $searchResult->results, Str::limit($searchResult->search_query, 100) . '.md', ['Content-Type' => 'text/markdown'])
                ->post('files', [
                    'purpose' => 'assistants',
                ]);

            if ((! $createFileResponse->successful()) || blank($createFileResponse->json('id'))) {
                report(new Exception('Failed to create research request search result file [' . $searchResult->getKey() . '] for vector store, as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

                continue;
            }

            $fileIds[] = $createFileResponse->json('id');
        }

        return $fileIds;
    }

    protected function findOrCreateVectorStoreRecordForFile(AiFile $file): OpenAiVectorStore
    {
        $deploymentHash = $this->getDeploymentHash();

        $vectorStore = OpenAiVectorStore::query()
            ->where('deployment_hash', $deploymentHash)
            ->whereMorphedTo('file', $file) /** @phpstan-ignore argument.type */
            ->first();

        if ($vectorStore) {
            return $vectorStore;
        }

        $vectorStore = new OpenAiVectorStore();
        $vectorStore->file()->associate($file); /** @phpstan-ignore argument.type */
        $vectorStore->deployment_hash = $deploymentHash;

        return $vectorStore;
    }

    protected function deleteExistingVectorStoreFile(AiFile $file, OpenAiVectorStore $vectorStore): void
    {
        if (blank($vectorStore->vector_store_file_id)) {
            return;
        }

        $deleteFileResponse = $this->filesHttpClient()
            ->delete("files/{$vectorStore->vector_store_file_id}");

        if (! $deleteFileResponse->successful()) {
            report(new Exception('Failed to delete file [' . $vectorStore->vector_store_file_id . '] associated with vector store [' . $vectorStore->vector_store_id . '] for file [' . $file->getKey() . '], as a [' . $deleteFileResponse->status() . '] response was returned: [' . $deleteFileResponse->body() . '].'));

            return;
        }

        $vectorStore->vector_store_file_id = null;
    }

    protected function isFileReadyInExistingVectorStore(AiFile $file, OpenAiVectorStore $vectorStore): ?bool
    {
        if (blank($vectorStore->vector_store_id)) {
            return null;
        }

        $getVectorStoreResponse = $this->vectorStoresHttpClient()
            ->get("vector_stores/{$vectorStore->vector_store_id}");

        $hasVectorStoreCompletedAllFiles = $getVectorStoreResponse->successful()
            && ($getVectorStoreResponse->json('status') === 'completed')
            && $getVectorStoreResponse->json('file_counts.completed')
            && ($getVectorStoreResponse->json('file_counts.completed') === $getVectorStoreResponse->json('file_counts.total'));

        $isVectorStoreProcessingFiles = $getVectorStoreResponse->successful()
            && ($getVectorStoreResponse->json('status') === 'in_progress')
            && $getVectorStoreResponse->json('file_counts.in_progress');

        if ((! $hasVectorStoreCompletedAllFiles) && $isVectorStoreProcessingFiles) {
            return false;
        } elseif (
            $hasVectorStoreCompletedAllFiles
            && $getVectorStoreResponse->json('expires_at')
            && (($vectorStoreExpiresAt = CarbonImmutable::createFromTimestampUTC($getVectorStoreResponse->json('expires_at')))->diffInHours() < -3)
        ) {
            $vectorStore->ready_until = $vectorStoreExpiresAt->subHours(2);
            $this->deleteExistingVectorStoreFile($file, $vectorStore);
            $vectorStore->save();

            return true;
        }

        $vectorStore->vector_store_id = null;

        return null;
    }

    protected function uploadNewFileForVectorStore(AiFile $file, OpenAiVectorStore $vectorStore): ?bool
    {
        if (filled($vectorStore->vector_store_file_id)) {
            return null;
        }

        $createFileResponse = $this->filesHttpClient()
            ->attach('file', $file->getParsingResults(), "{$file->getName()}.md", ['Content-Type' => 'text/markdown'])
            ->post('files', [
                'purpose' => 'assistants',
            ]);

        if ((! $createFileResponse->successful()) || blank($createFileResponse->json('id'))) {
            report(new Exception('Failed to create file [' . $file->getKey() . '] for vector store, as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

            $vectorStore->save();

            return false;
        }

        $vectorStore->vector_store_file_id = $createFileResponse->json('id');

        return null;
    }

    protected function resetMissingVectorStoreFileId(OpenAiVectorStore $vectorStore): void
    {
        if (blank($vectorStore->vector_store_file_id)) {
            return;
        }

        $getFileResponse = $this->filesHttpClient()
            ->get("files/{$vectorStore->vector_store_file_id}");

        if ($getFileResponse->successful()) {
            return;
        }

        $vectorStore->vector_store_file_id = null;
    }

    protected function createVectorStoreForFile(AiFile $file, OpenAiVectorStore $vectorStore): void
    {
        $createVectorStoreResponse = $this->vectorStoresHttpClient()
            ->acceptJson()
            ->asJson()
            ->post('vector_stores', [
                'name' => $file->getName(),
                'file_ids' => [$vectorStore->vector_store_file_id],
                'expires_after' => [
                    'anchor' => 'last_active_at',
                    'days' => $file instanceof AiMessageFile ? 7 : 28,
                ],
            ]);

        if ((! $createVectorStoreResponse->successful()) || blank($createVectorStoreResponse->json('id'))) {
            report(new Exception('Failed to create vector store for file [' . $file->getKey() . '], as a [' . $createVectorStoreResponse->status() . '] response was returned: [' . $createVectorStoreResponse->body() . '].'));

            $vectorStore->save();

            return;
        }

        $vectorStore->vector_store_id = $createVectorStoreResponse->json('id');
        $vectorStore->save();
    }

    protected function vectorStoresHttpClient(): PendingRequest
    {
        return Http::withHeaders([
            'api-key' => $this->getApiKey(),
        ])
            ->withQueryParameters(['api-version' => '2025-04-01-preview'])
            ->baseUrl((string) str($this->getDeployment())->beforeLast('/v1'));
    }

    protected function filesHttpClient(): PendingRequest
    {
        return Http::withHeaders([
            'api-key' => $this->getApiKey(),
        ])
            ->withQueryParameters(['api-version' => '2024-10-21'])
            ->baseUrl((string) str($this->getDeployment())->beforeLast('/v1'));
    }

    protected function streamResponse(Generator $stream, AiMessage $message, Closure $saveResponse): Closure
    {
        return function () use ($message, $saveResponse, $stream): Generator {
            try {
                // If the message was sent by the user, save the response to a new record.
                // If the message was sent by the assistant, and we are completing the response, save it to the existing record.
                $response = filled($message->user_id) ? (new AiMessage()) : $message;

                foreach ($stream as $chunk) {
                    if (
                        ($chunk->chunkType === ChunkType::Meta) &&
                        filled($chunk->meta?->id)
                    ) {
                        $response->message_id = $chunk->meta->id;

                        continue;
                    }

                    if ($chunk->chunkType !== ChunkType::Text) {
                        continue;
                    }

                    yield json_encode(['type' => 'content', 'content' => base64_encode($chunk->text)]);
                    $response->content .= $chunk->text;

                    if ($chunk->finishReason === FinishReason::Length) {
                        yield json_encode(['type' => 'content', 'content' => base64_encode('...'), 'incomplete' => true]);
                        $response->content .= '...';
                    }

                    if ($chunk->finishReason === FinishReason::Error) {
                        yield json_encode(['type' => 'failed', 'message' => 'An error happened when sending your message.']);

                        report(new MessageResponseException('Stream not successful.'));
                    }
                }

                $saveResponse($response);
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
        };
    }

    protected function generateAssistantInstructions(AiAssistant $assistant, bool $withDynamicContext = false): string
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
            $dynamicContext = rtrim(auth()->user()->getDynamicContext(), '. ');

            return "{$dynamicContext}.\n\n{$assistantInstructions}.\n\n{$formattingInstructions}";
        }

        return "{$assistantInstructions}.\n\n{$formattingInstructions}";
    }

    /**
     * @param array<string> $fileIds
     */
    protected function attachResearchRequestFilesToVectorStore(array $fileIds, OpenAiResearchRequestVectorStore $vectorStore): void
    {
        foreach ($fileIds as $fileId) {
            $attachFileResponse = $this->vectorStoresHttpClient()
                ->post("vector_stores/{$vectorStore->vector_store_id}/files", [
                    'file_id' => $fileId,
                ]);

            if ((! $attachFileResponse->successful()) || ! $attachFileResponse->json('id')) {
                report(new Exception('Failed to attach file [' . $fileId . '] to vector store [' . $vectorStore->vector_store_id . '], as a [' . $attachFileResponse->status() . '] response was returned: [' . $attachFileResponse->body() . '].'));
            }
        }
    }
}
