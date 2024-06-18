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

namespace AdvisingApp\Ai\Services;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Services\Contracts\SupportsFileUploads;
use AdvisingApp\Ai\DataTransferObjects\Files\FilesDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\Threads\ThreadsDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStoreFiles\VectorStoreFilesDataTransferObject;

class TestAiService implements Contracts\AiService, SupportsFileUploads
{
    use HasAiServiceHelpers;

    public array $uploadedFiles = [];

    public function createAssistant(AiAssistant $assistant): void {}

    public function updateAssistant(AiAssistant $assistant): void {}

    public function updateAssistantTools(AiAssistant $assistant, array $tools): void {}

    public function isAssistantExisting(AiAssistant $assistant): bool
    {
        return true;
    }

    public function createThread(AiThread $thread): void {}

    public function retrieveThread(AiThread $thread): ThreadsDataTransferObject
    {
        return ThreadsDataTransferObject::from([
            'id' => $thread->id,
            'vectorStoreIds' => [],
        ]);
    }

    public function modifyThread(AiThread $thread, array $parameters): ThreadsDataTransferObject
    {
        return ThreadsDataTransferObject::from([
            'id' => $thread->id,
            'vectorStoreIds' => [],
        ]);
    }

    public function deleteThread(AiThread $thread): void {}

    public function isThreadExisting(AiThread $thread): bool
    {
        return true;
    }

    public function createVectorStore(array $parameters): VectorStoresDataTransferObject
    {
        return VectorStoresDataTransferObject::from([
            'id' => fake()->uuid(),
            'name' => fake()->word(),
            'fileCounts' => [],
            'status' => 'processed',
            'expiresAt' => null,
        ]);
    }

    public function retrieveVectorStore(string $vectorStoreId): VectorStoresDataTransferObject
    {
        return VectorStoresDataTransferObject::from([
            'id' => $vectorStoreId,
            'name' => fake()->word(),
            'fileCounts' => [],
            'status' => 'processed',
            'expiresAt' => null,
        ]);
    }

    public function modifyVectorStore(string $vectorStoreId, array $parameters): void {}

    public function retrieveVectorStoreFiles(AiThread $thread, string $vectorStoreId, array $params): VectorStoreFilesDataTransferObject
    {
        return VectorStoreFilesDataTransferObject::from([
            'data' => [],
            'firstId' => fake()->uuid(),
            'lastId' => fake()->uuid(),
            'hasMore' => false,
        ]);
    }

    public function retrieveFile(AiMessageFile $file): FilesDataTransferObject
    {
        return FilesDataTransferObject::from([
            'id' => $file->file_id,
            'name' => fake()->word(),
            'status' => 'processed',
        ]);
    }

    public function withFiles(array $files): self
    {
        $this->uploadedFiles = $files;

        return $this;
    }

    public function sendMessage(AiMessage $message, Closure $saveResponse): Closure
    {
        $message->context = fake()->paragraph();
        $message->save();

        if (! empty($this->uploadedFiles)) {
            $files = $this->createFiles($message, $this->uploadedFiles);
            $message->files()->saveMany($files);
        }

        $responseContent = fake()->paragraph();

        return function () use ($responseContent, $saveResponse) {
            $response = new AiMessage();

            yield $responseContent;

            $response->content = $responseContent;

            $saveResponse($response);
        };
    }

    public function retryMessage(AiMessage $message, Closure $saveResponse): Closure
    {
        return $this->sendMessage($message, $saveResponse);
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        return 30000;
    }

    public function getDeployment(): ?string
    {
        return null;
    }

    public function getApiKey(): string
    {
        return 'test';
    }

    public function getApiVersion(): string
    {
        return '1.0.0';
    }

    public function supportsFileUploads(): bool
    {
        return true;
    }

    public function createFiles(AiMessage $message, array $files): Collection
    {
        return collect($files)->map(function ($file) {
            $fileRecord = new AiMessageFile();
            $fileRecord->temporary_url = 'temp-url';
            $fileRecord->name = 'test';
            $fileRecord->mime_type = 'text/plain';
            $fileRecord->file_id = Str::random(12);

            return $fileRecord;
        });
    }
}
