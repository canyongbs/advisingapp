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

namespace AdvisingApp\Ai\Services\Contracts;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Research\Models\ResearchRequest;
use Closure;
use Generator;
use Illuminate\Database\Eloquent\Model;
use Prism\Prism\Contracts\Message;

interface AiService
{
    /**
     * This method is passed a prompt and should return a completion for it.
     */
    public function complete(string $prompt, string $content, bool $shouldTrack = true): string;

    /**
     * This method is passed a prompt and should return a Base64 encoded image.
     */
    public function image(string $prompt): string;

    /**
     * This method is passed a prompt and message and should return a stream of the response.
     *
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function stream(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = []): Closure;

    /**
     * This method is passed a prompt and message and should return a stream of plain text chunks.
     *
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     * @param ?array<Message> $messages
     */
    public function streamRaw(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = [], ?array $messages = null, bool $hasImageGeneration = false): Closure;

    /**
     * This method is passed an unsaved `AiMessage` model and should send the
     * message to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function sendMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure;

    /**
     * This method is passed an `AiMessage` model and should recover the
     * request to the AI service. If that is successful, it should save the
     * message before fetching the response, in case the response fails
     * to generate.
     *
     * The method should return a new unsaved `AiMessage` model with the content
     * from the AI service set only, the other attributes will be set later.
     */
    public function retryMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure;

    public function completeResponse(AiMessage $response): Closure;

    public function getMaxAssistantInstructionsLength(): int;

    /**
     * @param array<AiFile> $files
     */
    public function areFilesReady(array $files, ?Model $context = null): bool;

    public function isResearchRequestReady(ResearchRequest $researchRequest): bool;

    public function afterResearchRequestSearchQueriesParsed(ResearchRequest $researchRequest): void;

    /**
     * @return array<string>
     */
    public function getResearchRequestRequestSearchQueries(ResearchRequest $researchRequest, string $prompt, string $content): array;

    /**
     * @return array{response: array<mixed>, nextRequestOptions: array<string, mixed>}
     */
    public function getResearchRequestRequestOutline(ResearchRequest $researchRequest, string $prompt, string $content): array;

    /**
     * @param array<string, mixed> $options
     */
    public function getResearchRequestRequestSection(ResearchRequest $researchRequest, string $prompt, string $content, array $options, Closure $nextRequestOptions): Generator;

    public function hasImageGeneration(): bool;
}
