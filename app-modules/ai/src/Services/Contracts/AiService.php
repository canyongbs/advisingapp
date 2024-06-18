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

namespace AdvisingApp\Ai\Services\Contracts;

use Closure;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\DataTransferObjects\Files\FilesDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\Threads\ThreadsDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStores\VectorStoresDataTransferObject;
use AdvisingApp\Ai\DataTransferObjects\VectorStoreFiles\VectorStoreFilesDataTransferObject;

interface AiService
{
    /**
     * This method is passed an unsaved `AiAssistant` model and should return
     * the model with any additional data that associates it with
     * the AI service, such as the `assistant_id`.
     */
    public function createAssistant(AiAssistant $assistant): void;

    /**
     * This method is passed an unsaved `AiAssistant` model and should update the
     * AI service with the new details.
     */
    public function updateAssistant(AiAssistant $assistant): void;

    /**
     * Override and update the existing tools set on an Assistant
     */
    public function updateAssistantTools(AiAssistant $assistant, array $tools): void;

    public function isAssistantExisting(AiAssistant $assistant): bool;

    public function ensureAssistantExists(AiAssistant $assistant): void;

    /**
     * This method is passed an unsaved `AiThread` model and should fill
     * the model with any additional data that associates it with
     * the AI service, such as the `thread_id`.
     */
    public function createThread(AiThread $thread): void;

    public function retrieveThread(AiThread $thread): ThreadsDataTransferObject;

    public function modifyThread(AiThread $thread, array $parameters): ThreadsDataTransferObject;

    /**
     * This method is passed an `AiThread` model and should trigger
     * it for deletion from the AI service.
     */
    public function deleteThread(AiThread $thread): void;

    public function isThreadExisting(AiThread $thread): bool;

    public function ensureAssistantAndThreadExists(AiThread $thread): void;

    /**
     * This method is passed an unsaved `AiMessage` model and should send the
     * message to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function sendMessage(AiMessage $message, Closure $saveResponse): Closure;

    public function withFiles(array $files): self;

    /**
     * This method is passed an `AiMessage` model and should recover the
     * request to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function retryMessage(AiMessage $message, Closure $saveResponse): Closure;

    public function getMaxAssistantInstructionsLength(): int;

    public function retrieveFile(AiMessageFile $file): FilesDataTransferObject;

    public function createVectorStore(array $parameters): VectorStoresDataTransferObject;

    public function retrieveVectorStore(string $vectorStoreId): VectorStoresDataTransferObject;

    public function modifyVectorStore(string $vectorStoreId, array $parameters): void;

    public function retrieveVectorStoreFiles(AiThread $thread, string $vectorStoreId, array $params): VectorStoreFilesDataTransferObject;

    public function getDeployment(): ?string;

    public function getApiKey(): string;

    public function getApiVersion(): string;
}
