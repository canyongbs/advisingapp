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

namespace AdvisingApp\IntegrationOpenAi\Services\Concerns;

use AdvisingApp\Ai\Exceptions\UploadedFileCouldNotBeProcessed;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\Files\FilesDataTransferObject;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\VectorStoreFiles\VectorStoreFilesDataTransferObject;
use AdvisingApp\IntegrationOpenAi\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;
use AdvisingApp\IntegrationOpenAi\Exceptions\FileUploadException;
use CURLFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

trait UploadsFiles
{
    public function afterThreadSelected(AiThread $thread): void
    {
        if (! $this->isThreadExisting($thread)) {
            return;
        }

        if (! $thread->messages()->exists()) {
            return;
        }

        if (! is_null($expiredVectorStores = $this->getExpiredVectorStoresForThread($thread))) {
            foreach ($expiredVectorStores as $expiredVectorStore) {
                $this->recreateVectorStoreForThread($thread, $expiredVectorStore);
            }
        }
    }

    public function afterLoadFirstThread(AiThread $thread): void
    {
        $this->afterThreadSelected($thread);
    }

    /**
     * @param Collection<int, AiAssistantFile>  $files
     *
     * @return Collection<int, AiAssistantFile>
     */
    public function createAssistantFiles(AiAssistant $assistant, Collection $files): Collection
    {
        return $files->each(function (AiAssistantFile $fileRecord) {
            $fileRecord->file_id = $this->uploadFileToClient($fileRecord);

            $fileRecord->addMediaFromUrl($fileRecord->temporary_url)->toMediaCollection('file');

            $fileRecord->save();

            return $fileRecord;
        });
    }

    public function deleteAssistantFile(AiAssistantFile $file): void
    {
        $this->client->files()->delete($file->file_id);
    }

    public function updateAssistantVectorStoreId(AiAssistant $assistant, string $vectorStoreId): void
    {
        $this->client->assistants()->modify($assistant->assistant_id, [
            'tool_resources' => [
                'file_search' => [
                    'vector_store_ids' => [$vectorStoreId],
                ],
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function createVectorStore(array $parameters): VectorStoresDataTransferObject
    {
        $response = $this->client->vectorStores()->create($parameters);

        return VectorStoresDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->name,
            'fileCounts' => get_object_vars($response->fileCounts),
            'status' => $response->status,
            'expiresAt' => $response->expiresAt,
        ]);
    }

    public function deleteVectorStore(string $vectorStoreId): void
    {
        $this->client->vectorStores()->delete($vectorStoreId);
    }

    public function awaitVectorStoreProcessing(VectorStoresDataTransferObject $vectorStore): void
    {
        $timeout = 60;

        $vectorStoreResponseStatus = $vectorStore->status;

        while ($vectorStoreResponseStatus !== 'completed') {
            if ($timeout <= 0) {
                throw new UploadedFileCouldNotBeProcessed();
            }

            usleep(500000);

            $vectorStoreResponseStatus = $this->retrieveVectorStore($vectorStore->id)->status;

            $timeout -= 0.5;
        }
    }

    /**
     * @param array<string> $fileIds
     */
    public function createVectorStoreFilesBatch(string $vectorStoreId, array $fileIds): void
    {
        $this->client->vectorStores()->batches()->create(
            vectorStoreId: $vectorStoreId,
            parameters: [
                'file_ids' => $fileIds,
            ]
        );
    }

    public function retrieveVectorStore(string $vectorStoreId): VectorStoresDataTransferObject
    {
        $response = $this->client->vectorStores()->retrieve($vectorStoreId);

        return VectorStoresDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->name,
            'fileCounts' => get_object_vars($response->fileCounts),
            'status' => $response->status,
            'expiresAt' => $response->expiresAt,
        ]);
    }

    /**
     * The `openai-php/client` does not current work with the `GET /vector_stores/{vectorStoreId}/files` endpoint
     * for Azure Open AI. This is due to the expectation of a `chunking_strategy` key in the response, which Azure
     * does not provide. An issue has been opened, but this request needs to happen without the client for now.
     *
     * @param array<string> $params
     */
    public function retrieveVectorStoreFiles(string $vectorStoreId, array $params = []): VectorStoreFilesDataTransferObject
    {
        $response = Http::withHeaders([
            'api-key' => $this->getApiKey(),
            'OpenAI-Beta' => 'assistants=v2',
            'Content-Type' => 'application/json',
        ])
            ->withQueryParameters(
                $params,
            )
            ->get($this->getDeployment() . '/vector_stores/' . $vectorStoreId . '/files', [
                'api-version' => $this->getApiVersion(),
            ]);

        return VectorStoreFilesDataTransferObject::from([
            'object' => $response->json()['object'],
            'data' => $response->json()['data'],
            'firstId' => $response->json()['first_id'],
            'lastId' => $response->json()['last_id'],
            'hasMore' => $response->json()['has_more'],
        ]);
    }

    public function deleteFile(AiFile $file): void
    {
        try {
            $this->client->files()->delete($file->getFileId());
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @param array<int, mixed|AiMessageFile> $files
     *
     * @return array<AiMessageFile>
     */
    protected function createFiles(array $files): array
    {
        return array_map(
            function (array | AiMessageFile $file): AiMessageFile {
                if ($file instanceof AiMessageFile) {
                    return $file;
                }

                $fileRecord = new AiMessageFile();
                $fileRecord->temporary_url = $file['temporaryUrl'];
                $fileRecord->name = $file['name'];
                $fileRecord->mime_type = $file['mimeType'];

                $fileRecord->file_id = $this->uploadFileToClient($fileRecord);

                $fileRecord->addMediaFromUrl($fileRecord->temporary_url)->toMediaCollection('files');

                return $fileRecord;
            },
            $files,
        );
    }

    protected function uploadFileToClient(AiFile $file): string
    {
        $apiKey = $this->getApiKey();
        $apiVersion = $this->getApiVersion();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->getDeployment() . '/files?api-version=' . $apiVersion);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $cfile = new CURLFile($file->getTemporaryUrl(), $file->getMimeType(), $file->getName());

        $postFields = [
            'purpose' => 'assistants',
            'file' => $cfile,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $headers = [
            'api-key: ' . $apiKey,
            'OpenAI-Beta: assistants=v2',
            'Accept: */*',
            'Content-Type: multipart/form-data',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if (curl_errno($ch) || ! isset($response['id'])) {
            if (! blank(curl_error($ch))) {
                throw new FileUploadException(curl_error($ch));
            }

            throw new FileUploadException();
        }

        if ($response['status'] === 'error') {
            throw new FileUploadException('The uploaded file could not be processed. Please try again, or upload a different file.');
        }

        $maxTries = 5;
        $tries = 0;
        $status = $response['status'];

        while ($status !== 'processed' && $tries < $maxTries) {
            usleep(500000);
            $response = $this->retrieveFile($file);
            $status = $response->status;
            $tries++;
        }

        if ($status !== 'processed') {
            throw new FileUploadException('The uploaded file could not be processed. Please try again, or upload a different file.');
        }

        curl_close($ch);

        return $response['id'];
    }

    protected function retrieveFile(AiFile $file): FilesDataTransferObject
    {
        $response = $this->client->files()->retrieve($file->getFileId());

        return FilesDataTransferObject::from([
            'id' => $response->id,
            'name' => $response->filename,
            'status' => $response->status,
        ]);
    }

    /**
     * @param array<string> $vectorStoreFileIds
     * @param string|null $after
     */
    protected function retrieveAllVectorStoreFileIds(AiThread $thread, string $vectorStoreId, array &$vectorStoreFileIds = [], ?string $after = null): void
    {
        $params = [];

        if ($after !== null) {
            $params['after'] = $after;
        }

        $response = $this->retrieveVectorStoreFiles($vectorStoreId, $params);

        collect($response->data)->each(function ($file) use (&$vectorStoreFileIds) {
            $vectorStoreFileIds[] = $file['id'];
        });

        if ($response->hasMore === true) {
            $this->retrieveAllVectorStoreFileIds(
                thread: $thread,
                vectorStoreId: $vectorStoreId,
                vectorStoreFileIds: $vectorStoreFileIds,
                after: $response->lastId
            );
        }
    }

    /**
     * @param array<string, mixed> $vectorStore
     */
    protected function recreateVectorStoreForThread(AiThread $thread, array $vectorStore): void
    {
        $vectorStoreFileIds = [];

        $this->retrieveAllVectorStoreFileIds(
            thread: $thread,
            vectorStoreId: $vectorStore['id'],
            vectorStoreFileIds: $vectorStoreFileIds
        );

        // Create new vector store
        $newVectorStore = $this->createVectorStore([
            'file_ids' => $vectorStoreFileIds,
            'name' => 'Refreshed vector store ' . now()->timestamp . ' for thread' . $thread->id,
        ]);

        // Update the thread to use the new vector store.
        $this->modifyThread($thread, [
            'tool_resources' => [
                'file_search' => [
                    'vector_store_ids' => [$newVectorStore->id],
                ],
            ],
        ]);

        // Ensure the new vector store has processed all of its files.
        $this->awaitVectorStoreProcessing($newVectorStore);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getExpiredVectorStoresForThread(AiThread $thread): ?array
    {
        if (! $this->supportsMessageFileUploads()) {
            return null;
        }

        $thread = $this->retrieveThread($thread);

        // Currently threads only support a single vector store
        $expiredVectorStores = collect($thread->vectorStoreIds)
            ->map(function ($vectorStoreId) {
                $vectorStoreResponse = $this->retrieveVectorStore($vectorStoreId);

                if ($vectorStoreResponse->status === 'expired') {
                    return $vectorStoreResponse;
                }

                return null;
            })
            ->filter()
            ->toArray();

        return ! empty($expiredVectorStores) ? $expiredVectorStores : null;
    }
}
