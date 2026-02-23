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

namespace AdvisingApp\IntegrationOpenAi\Services\BaseOpenAiService\Concerns;

use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

trait InteractsWithVectorStores
{
    protected function vectorStoresHttpClient(): PendingRequest
    {
        return Http::withHeaders([
            'api-key' => $this->getApiKey(),
        ])
            ->withQueryParameters(['api-version' => '2025-04-01-preview'])
            ->baseUrl($this->getDeployment());
    }

    protected function filesHttpClient(): PendingRequest
    {
        return Http::withHeaders([
            'api-key' => $this->getApiKey(),
        ])
            ->withQueryParameters(['api-version' => '2024-10-21'])
            ->baseUrl($this->getDeployment());
    }

    /**
     * @param array<AiFile> $files
     *
     * @return array<OpenAiVectorStore>
     */
    protected function getValidExistingVectorStoresForFiles(array $files, ?Model $context = null): array
    {
        $vectorStores = $this->getExistingVectorStoresForFiles($files, $context);

        foreach ($vectorStores as $vectorStoreIndex => $vectorStore) {
            $vectorStoreId ??= $vectorStore->vector_store_id;

            if (filled($vectorStore->vector_store_id) && ($vectorStore->vector_store_id !== $vectorStoreId)) {
                $this->deleteVectorStore($vectorStore);

                unset($vectorStores[$vectorStoreIndex]);
            }
        }

        return $vectorStores;
    }

    /**
     * @param array<AiFile> $files
     */
    protected function deleteExpiredVectorStoresForFiles(array $files, ?Model $context = null): void
    {
        if (! $files) {
            return;
        }

        $query = OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereMorphedTo('file', $files) /** @phpstan-ignore argument.type */
            ->whereNotNull('ready_until')
            ->where('ready_until', '<', now())
            ->whereNotNull('vector_store_id');

        $this->scopeVectorStoreQueryByContext($query, $context);

        $vectorStores = $query->get();

        foreach ($vectorStores as $vectorStore) {
            $this->deleteVectorStore($vectorStore);
        }
    }

    /**
     * @param array<AiFile> $files
     *
     * @return array<OpenAiVectorStore>
     */
    protected function getExistingVectorStoresForFiles(array $files, ?Model $context = null): array
    {
        if (! $files) {
            return [];
        }

        $query = OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->whereMorphedTo('file', $files) /** @phpstan-ignore argument.type */
            ->whereNotNull('vector_store_id');

        $this->scopeVectorStoreQueryByContext($query, $context);

        return $query->get()->all();
    }

    protected function deleteVectorStore(OpenAiVectorStore $vectorStore): void
    {
        if (! $vectorStore->exists()) {
            return;
        }

        foreach ($this->getVectorStoreFileIds($vectorStore) as $fileId) {
            $this->deleteFile($fileId);
        }

        $deleteVectorStoreResponse = $this->vectorStoresHttpClient()
            ->delete("vector_stores/{$vectorStore->vector_store_id}");

        if ((! $deleteVectorStoreResponse->successful()) && (! $deleteVectorStoreResponse->notFound())) {
            report(new Exception('Failed to delete vector store [' . $vectorStore->vector_store_id . '], as a [' . $deleteVectorStoreResponse->status() . '] response was returned: [' . $deleteVectorStoreResponse->body() . '].'));
        }

        OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->where('vector_store_id', $vectorStore->vector_store_id)
            ->delete();
    }

    /**
     * @return array<string>
     */
    protected function getVectorStoreFileIds(OpenAiVectorStore $vectorStore): array
    {
        $listVectorStoreFilesResponse = $this->vectorStoresHttpClient()
            ->get("vector_stores/{$vectorStore->vector_store_id}/files");

        if ((! $listVectorStoreFilesResponse->successful()) || (! is_array($listVectorStoreFilesResponse->json('data')))) {
            report(new Exception('Failed to list files for vector store [' . $vectorStore->vector_store_id . '], as a [' . $listVectorStoreFilesResponse->status() . '] response was returned: [' . $listVectorStoreFilesResponse->body() . '].'));

            return [];
        }

        return Arr::pluck($listVectorStoreFilesResponse->json('data'), 'id');
    }

    protected function deleteFile(string $fileId): void
    {
        $deleteFileResponse = $this->filesHttpClient()
            ->delete("files/{$fileId}");

        if ((! $deleteFileResponse->successful()) && (! $deleteFileResponse->notFound())) {
            report(new Exception('Failed to delete file [' . $fileId . '], as a [' . $deleteFileResponse->status() . '] response was returned: [' . $deleteFileResponse->body() . '].'));
        }
    }

    /**
     * @param array<AiFile> $newFiles
     */
    protected function deleteOldFilesFromVectorStore(OpenAiVectorStore $vectorStore, array $newFiles, ?Model $context = null): void
    {
        if (! $newFiles) {
            return;
        }

        $query = OpenAiVectorStore::query()
            ->where('deployment_hash', $this->getDeploymentHash())
            ->where('vector_store_id', $vectorStore->vector_store_id)
            ->whereNot(fn (Builder $query) => $query->whereMorphedTo('file', $newFiles)) /** @phpstan-ignore argument.type */
            ->whereNotNull('vector_store_file_id');

        $this->scopeVectorStoreQueryByContext($query, $context);

        $oldFilesInVectorStore = $query->get();

        foreach ($oldFilesInVectorStore as $fileToDelete) {
            $this->removeFileFromVectorStore($vectorStore, $fileToDelete->vector_store_file_id);
            $this->deleteFile($fileToDelete->vector_store_file_id);

            $fileToDelete->delete();
        }
    }

    protected function removeFileFromVectorStore(OpenAiVectorStore $vectorStore, string $fileId): void
    {
        $removeFileResponse = $this->vectorStoresHttpClient()
            ->delete("vector_stores/{$vectorStore->vector_store_id}/files/{$fileId}");

        if ((! $removeFileResponse->successful()) && (! $removeFileResponse->notFound())) {
            report(new Exception('Failed to remove file [' . $fileId . '] from vector store [' . $vectorStore->vector_store_id . '], as a [' . $removeFileResponse->status() . '] response was returned: [' . $removeFileResponse->body() . '].'));
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getVectorStoreState(OpenAiVectorStore $vectorStore): ?array
    {
        $getVectorStoreResponse = $this->vectorStoresHttpClient()
            ->get("vector_stores/{$vectorStore->vector_store_id}");

        if ((! $getVectorStoreResponse->successful()) || (! is_array($getVectorStoreResponse->json()))) {
            report(new Exception('Failed to get vector store state for vector store [' . $vectorStore->vector_store_id . '], as a [' . $getVectorStoreResponse->status() . '] response was returned: [' . $getVectorStoreResponse->body() . '].'));

            return null;
        }

        return $getVectorStoreResponse->json();
    }

    /**
     * @param array<AiFile> $files
     */
    protected function createVectorStoreForFiles(array $files, ?Model $context = null): void
    {
        $vectorStores = [];

        foreach ($files as $file) {
            if ($vectorStore = $this->uploadFileForVectorStore($file, $context)) {
                $vectorStores[] = $vectorStore;
            }
        }

        $initialVectorStores = array_slice($vectorStores, 0, 100);
        $remainingVectorStores = array_slice($vectorStores, 100);

        $createVectorStoreResponse = $this->vectorStoresHttpClient()
            ->acceptJson()
            ->asJson()
            ->post('vector_stores', [
                'name' => Arr::first($files)->getName(),
                'file_ids' => Arr::pluck($initialVectorStores, 'vector_store_file_id'),
                'expires_after' => [
                    'anchor' => 'last_active_at',
                    'days' => Arr::first($files) instanceof AiMessageFile ? 7 : 28,
                ],
            ]);

        if ((! $createVectorStoreResponse->successful()) || blank($createVectorStoreResponse->json('id'))) {
            report(new Exception('Failed to create vector store, as a [' . $createVectorStoreResponse->status() . '] response was returned: [' . $createVectorStoreResponse->body() . '].'));

            foreach ($vectorStores as $vectorStore) {
                $vectorStore->save();
            }

            return;
        }

        $vectorStoreId = $createVectorStoreResponse->json('id');

        foreach ($initialVectorStores as $vectorStore) {
            $vectorStore->vector_store_id = $vectorStoreId;
            $vectorStore->save();
        }

        foreach ($remainingVectorStores as $remainingVectorStore) {
            $this->attachFileToVectorStore($vectorStoreId, $remainingVectorStore);
        }
    }

    protected function uploadFileForVectorStore(AiFile $file, ?Model $context = null): ?OpenAiVectorStore
    {
        $vectorStore = new OpenAiVectorStore();
        $vectorStore->file()->associate($file); /** @phpstan-ignore argument.type */
        $vectorStore->deployment_hash = $this->getDeploymentHash();

        if ($context) {
            $vectorStore->context()->associate($context);
        }

        $parsingResults = $file->getParsingResults();
        $name = $file->getName();

        if ((blank($parsingResults) || blank($name)) && ($file instanceof Model)) {
            $lazyLoadedFile = $file->fresh();

            $parsingResults = $lazyLoadedFile->getParsingResults();
            $name = $lazyLoadedFile->getName();
        }

        $createFileResponse = $this->filesHttpClient()
            ->attach('file', $parsingResults, (string) str($name)->limit(100)->slug()->append('.md'), ['Content-Type' => 'text/markdown'])
            ->post('files', [
                'purpose' => 'assistants',
            ]);

        if ((! $createFileResponse->successful()) || blank($createFileResponse->json('id'))) {
            report(new Exception('Failed to create file [' . $file->getKey() . '] for vector store, as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

            $vectorStore->save();

            return null;
        }

        $vectorStore->vector_store_file_id = $createFileResponse->json('id');

        return $vectorStore;
    }

    protected function uploadMissingFileToVectorStore(OpenAiVectorStore $vectorStore, AiFile $file, ?Model $context = null): void
    {
        $fileVectorStore = $this->uploadFileForVectorStore($file, $context);

        if (blank($fileVectorStore)) {
            return;
        }

        $this->attachFileToVectorStore($vectorStore->vector_store_id, $fileVectorStore);
    }

    protected function attachFileToVectorStore(string $vectorStoreId, OpenAiVectorStore $fileVectorStore): void
    {
        $createFileResponse = $this->vectorStoresHttpClient()
            ->post("vector_stores/{$vectorStoreId}/files", [
                'file_id' => $fileVectorStore->vector_store_file_id,
            ]);

        if ((! $createFileResponse->successful()) || ! $createFileResponse->json('id')) {
            report(new Exception('Failed to attach file [' . $fileVectorStore->vector_store_file_id . '] to vector store [' . $vectorStoreId . '], as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

            $fileVectorStore->save();

            return;
        }

        $fileVectorStore->vector_store_id = $vectorStoreId;
        $fileVectorStore->save();
    }

    protected function updateFileInVectorStore(OpenAiVectorStore $vectorStore, OpenAiVectorStore $oldVectorStore, AiFile $file, ?Model $context = null): void
    {
        $newFileVectorStore = $this->uploadFileForVectorStore($file, $context);

        if (blank($newFileVectorStore)) {
            return;
        }

        $createFileResponse = $this->vectorStoresHttpClient()
            ->post("vector_stores/{$vectorStore->vector_store_id}/files", [
                'file_id' => $newFileVectorStore->vector_store_file_id,
            ]);

        if ((! $createFileResponse->successful()) || ! $createFileResponse->json('id')) {
            report(new Exception('Failed to attach updated file [' . $newFileVectorStore->vector_store_file_id . '] to vector store [' . $vectorStore->vector_store_id . '], as a [' . $createFileResponse->status() . '] response was returned: [' . $createFileResponse->body() . '].'));

            $newFileVectorStore->save();

            return;
        }

        if (filled($oldVectorStore->vector_store_file_id)) {
            $this->removeFileFromVectorStore($vectorStore, $oldVectorStore->vector_store_file_id);
            $this->deleteFile($oldVectorStore->vector_store_file_id);
        }

        $oldVectorStore->vector_store_file_id = $newFileVectorStore->vector_store_file_id;
        $oldVectorStore->ready_until = null;
        $oldVectorStore->save();
    }

    /**
     * @param array<string, mixed> $vectorStoreState
     */
    protected function evaluateVectorStoreState(OpenAiVectorStore $vectorStore, array $vectorStoreState): bool
    {
        $hasVectorStoreCompletedAllFiles = ($vectorStoreState['status'] === 'completed')
            && $vectorStoreState['file_counts']['completed']
            && ($vectorStoreState['file_counts']['completed'] === $vectorStoreState['file_counts']['total']);

        $isVectorStoreProcessingFiles = ($vectorStoreState['status'] === 'in_progress')
            && $vectorStoreState['file_counts']['in_progress'];

        if ((! $hasVectorStoreCompletedAllFiles) && $isVectorStoreProcessingFiles) {
            return false;
        }

        if (
            $hasVectorStoreCompletedAllFiles
            && $vectorStoreState['expires_at']
            && (($vectorStoreExpiresAt = CarbonImmutable::createFromTimestampUTC($vectorStoreState['expires_at']))->diffInHours() < -3)
        ) {
            OpenAiVectorStore::query()
                ->where('deployment_hash', $this->getDeploymentHash())
                ->where('vector_store_id', $vectorStore->vector_store_id)
                ->update([
                    'ready_until' => $vectorStoreExpiresAt->subHours(2),
                ]);

            return true;
        }

        $this->deleteVectorStore($vectorStore);

        return false;
    }

    protected function scopeVectorStoreQueryByContext(Builder $query, ?Model $context): void
    {
        if ($context) {
            $query->where('context_type', $context->getMorphClass())
                ->where('context_id', $context->getKey());
        } else {
            $query->whereNull('context_type');
        }
    }
}
