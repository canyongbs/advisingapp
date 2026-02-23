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

namespace AdvisingApp\Ai\Services;

use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Ai\Support\StreamingChunks\Finish;
use AdvisingApp\Ai\Support\StreamingChunks\Text;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use AdvisingApp\Research\Models\ResearchRequest;
use Closure;
use Exception;
use Generator;
use Illuminate\Database\Eloquent\Model;
use Prism\Prism\Contracts\Message;

class TestAiService implements AiService
{
    public function complete(string $prompt, string $content, bool $shouldTrack = true): string
    {
        if ($shouldTrack) {
            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }

        return fake()->paragraph();
    }

    public function image(string $prompt): string
    {
        return '';
    }

    /**
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     */
    public function stream(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = []): Closure
    {
        throw new Exception('Plain text streaming is not supported by this service.');
    }

    /**
     * @param array<AiFile> $files
     * @param array<string, mixed> $options
     * @param ?array<Message> $messages
     */
    public function streamRaw(string $prompt, string $content, array $files = [], bool $shouldTrack = true, array $options = [], ?array $messages = null, bool $hasImageGeneration = false): Closure
    {
        throw new Exception('Plain text streaming is not supported by this service.');
    }

    public function sendMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        $message->context = fake()->paragraph();
        $message->save();

        $message->thread->name = fake()->words();
        $message->thread->save();

        if ($message->wasRecentlyCreated || $message->wasChanged('content')) {
            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }

        if (! empty($files)) {
            $message->files()->saveMany($files);
        }

        return function (): Generator {
            yield new Text(fake()->paragraph());

            yield new Finish();
        };
    }

    public function retryMessage(AiMessage $message, array $files, bool $hasImageGeneration = false): Closure
    {
        return $this->sendMessage($message, $files);
    }

    public function completeResponse(AiMessage $response): Closure
    {
        return $this->sendMessage($response, files: []);
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        return 30000;
    }

    /**
     * @param array<AiFile> $files
     */
    public function areFilesReady(array $files, ?Model $context = null): bool
    {
        return true;
    }

    public function isResearchRequestReady(ResearchRequest $researchRequest): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getResearchRequestRequestSearchQueries(ResearchRequest $researchRequest, string $prompt, string $content): array
    {
        return [];
    }

    /**
     * @return array{response: array<mixed>, nextRequestOptions: array<string, mixed>}
     */
    public function getResearchRequestRequestOutline(ResearchRequest $researchRequest, string $prompt, string $content): array
    {
        return ['response' => [], 'nextRequestOptions' => []];
    }

    /**
     * @param array<string, mixed> $options
     */
    public function getResearchRequestRequestSection(ResearchRequest $researchRequest, string $prompt, string $content, array $options, Closure $nextRequestOptions): Generator
    {
        yield fake()->paragraph();
    }

    public function afterResearchRequestSearchQueriesParsed(ResearchRequest $researchRequest): void {}

    public function hasImageGeneration(): bool
    {
        return false;
    }
}
