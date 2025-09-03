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

namespace AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService\Concerns;

use AdvisingApp\Ai\Exceptions\MessageResponseException;
use AdvisingApp\Ai\Settings\AiSettings;
use AdvisingApp\Ai\Support\StreamingChunks\NextRequestOptions;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiResearchRequestVectorStore;
use AdvisingApp\Research\Models\ResearchRequest;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Prism\Prism\Enums\ChunkType;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use Throwable;

trait InteractsWithResearchRequests
{
    /**
     * @return array<string>
     */
    public function getResearchRequestRequestSearchQueries(ResearchRequest $researchRequest, string $prompt, string $content): array
    {
        return $this->structured(
            prompt: $prompt,
            content: $content,
            schema: app(ArraySchema::class, [
                'name' => 'search_queries',
                'description' => 'An array of search queries to be used for web searches.',
                'items' => app(StringSchema::class, [
                    'name' => 'query',
                    'description' => 'A search query that can be used in Google to find relevant web pages.',
                ]),
            ]),
            providerOptions: [
                'tool_choice' => [
                    'type' => 'file_search',
                ],
                'tools' => [[
                    'type' => 'file_search',
                    'vector_store_ids' => $this->getReadyResearchRequestVectorStoreIds($researchRequest),
                ]],
            ],
        )['description'] ?? [];
    }

    /**
     * @return array{response: array<mixed>, nextRequestOptions: array<string, mixed>}
     */
    public function getResearchRequestRequestOutline(ResearchRequest $researchRequest, string $prompt, string $content): array
    {
        $responseId = null;

        $response = $this->structured(
            prompt: $prompt,
            content: $content,
            schema: app(ObjectSchema::class, [
                'name' => 'outline',
                'description' => 'An outline for the research report, including sections and subsections.',
                'properties' => [
                    app(ObjectSchema::class, [
                        'name' => 'abstract',
                        'description' => 'An abstract for the research report.',
                        'properties' => [
                            app(StringSchema::class, [
                                'name' => 'heading',
                                'description' => 'The heading of the abstract section.',
                            ]),
                        ],
                        'requiredFields' => ['heading'],
                    ]),
                    app(ObjectSchema::class, [
                        'name' => 'introduction',
                        'description' => 'An introduction for the research report.',
                        'properties' => [
                            app(StringSchema::class, [
                                'name' => 'heading',
                                'description' => 'The heading of the introduction section.',
                            ]),
                        ],
                        'requiredFields' => ['heading'],
                    ]),
                    app(ArraySchema::class, [
                        'name' => 'sections',
                        'description' => 'An array of sections for the research report, each with a heading and subsections. There should be 6 sections in total.',
                        'items' => app(ObjectSchema::class, [
                            'name' => 'section',
                            'description' => 'A section in the research report.',
                            'properties' => [
                                app(StringSchema::class, [
                                    'name' => 'heading',
                                    'description' => 'The heading of the section.',
                                ]),
                                app(ArraySchema::class, [
                                    'name' => 'subsections',
                                    'description' => 'An array of subsections within the section. There should be 3 subsections in this section.',
                                    'items' => app(ObjectSchema::class, [
                                        'name' => 'subsection',
                                        'description' => 'A subsection within a section of the research report.',
                                        'properties' => [
                                            app(StringSchema::class, [
                                                'name' => 'heading',
                                                'description' => 'The heading of the subsection.',
                                            ]),
                                        ],
                                        'requiredFields' => ['heading'],
                                    ]),
                                ]),
                            ],
                            'requiredFields' => ['heading', 'subsections'],
                        ]),
                    ]),
                    app(ObjectSchema::class, [
                        'name' => 'conclusion',
                        'description' => 'A conclusion for the research report.',
                        'properties' => [
                            app(StringSchema::class, [
                                'name' => 'heading',
                                'description' => 'The heading of the conclusion section.',
                            ]),
                        ],
                        'requiredFields' => ['heading'],
                    ]),
                ],
                'requiredFields' => ['abstract', 'introduction', 'sections', 'conclusion'],
            ]),
            providerOptions: [
                'tool_choice' => [
                    'type' => 'file_search',
                ],
                'tools' => [[
                    'type' => 'file_search',
                    'vector_store_ids' => $this->getReadyResearchRequestVectorStoreIds($researchRequest),
                ]],
            ],
            responseId: $responseId,
        );

        return [
            'response' => $response['properties'] ?? [],
            'nextRequestOptions' => filled($responseId) ? [
                'previous_response_id' => $responseId,
            ] : [],
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    public function getResearchRequestRequestSection(ResearchRequest $researchRequest, string $prompt, string $content, array $options, Closure $nextRequestOptions): Generator
    {
        $aiSettings = app(AiSettings::class);

        try {
            $stream = Prism::text()
                ->using('azure_open_ai', $this->getModel())
                ->withClientOptions([
                    'apiKey' => $this->getApiKey(),
                    'apiVersion' => $this->getApiVersion(),
                    'deployment' => $this->getDeployment(),
                ])
                ->withProviderOptions([
                    'tool_choice' => [
                        'type' => 'file_search',
                    ],
                    'tools' => [[
                        'type' => 'file_search',
                        'vector_store_ids' => $this->getReadyResearchRequestVectorStoreIds($researchRequest),
                    ]],
                    'truncation' => 'auto',
                    ...$options,
                ])
                ->withSystemPrompt($prompt)
                ->withPrompt($content)
                ->usingTemperature($this->hasTemperature() ? $aiSettings->temperature : null)
                ->asStream();

            foreach ($stream as $chunk) {
                if (
                    ($chunk->chunkType === ChunkType::Meta) &&
                    filled($chunk->meta?->id)
                ) {
                    $nextRequestOptions(['previous_response_id' => $chunk->meta->id]);

                    continue;
                }

                if ($chunk->chunkType !== ChunkType::Text) {
                    continue;
                }

                yield $chunk->text;

                if ($chunk->finishReason === FinishReason::Error) {
                    report(new MessageResponseException('Stream not successful.'));
                }
            }
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
            ->whereNotNull('vector_store_id')
            ->pluck('vector_store_id')
            ->all();
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

            if ((! $deleteFileResponse->successful()) && (! $deleteFileResponse->notFound())) {
                report(new Exception('Failed to delete file [' . $fileId . '] associated with vector store [' . $vectorStore->vector_store_id . '] for research request [' . $researchRequest->getKey() . '], as a [' . $deleteFileResponse->status() . '] response was returned: [' . $deleteFileResponse->body() . '].'));
            }
        }
    }

    protected function createVectorStoreForResearchRequest(ResearchRequest $researchRequest, OpenAiResearchRequestVectorStore $vectorStore): void
    {
        $fileIds = $this->uploadResearchRequestFilesForVectorStore($researchRequest, $vectorStore);

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
                ->attach('file', $file->results, (string) str($file->media->name)->limit(100)->slug()->append('.md'), ['Content-Type' => 'text/markdown'])
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
                ->attach('file', $link->results, (string) str($link->url)->limit(100)->slug()->append('.md'), ['Content-Type' => 'text/markdown'])
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
                ->attach('file', $searchResult->results, (string) str($searchResult->search_query)->limit(100)->slug()->append('.md'), ['Content-Type' => 'text/markdown'])
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
