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

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Services\Concerns\HasAiServiceHelpers;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TestAiService implements AiService
{
    use HasAiServiceHelpers;

    public function complete(string $prompt, string $content): string
    {
        dispatch(new RecordTrackedEvent(
            type: TrackedEventType::AiExchange,
            occurredAt: now(),
        ));

        return fake()->paragraph();
    }

    public function createAssistant(AiAssistant $assistant): void {}

    public function updateAssistant(AiAssistant $assistant): void {}

    public function isAssistantExisting(AiAssistant $assistant): bool
    {
        return true;
    }

    public function createThread(AiThread $thread): void {}

    public function deleteThread(AiThread $thread): void {}

    public function isThreadExisting(AiThread $thread): bool
    {
        return true;
    }

    public function sendMessage(AiMessage $message, array $files, Closure $saveResponse): Closure
    {
        $message->context = fake()->paragraph();
        $message->save();

        if ($message->wasRecentlyCreated || $message->wasChanged('content')) {
            dispatch(new RecordTrackedEvent(
                type: TrackedEventType::AiExchange,
                occurredAt: now(),
            ));
        }

        if (! empty($files)) {
            $createdFiles = $this->createFiles($message, $files);
            $message->files()->saveMany($createdFiles);
        }

        $responseContent = fake()->paragraph();

        return function () use ($responseContent, $saveResponse) {
            $response = new AiMessage();

            yield $responseContent;

            $response->content = $responseContent;

            $saveResponse($response);
        };
    }

    public function retryMessage(AiMessage $message, array $files, Closure $saveResponse): Closure
    {
        return $this->sendMessage($message, $files, $saveResponse);
    }

    public function completeResponse(AiMessage $response, array $files, Closure $saveResponse): Closure
    {
        if (! empty($files)) {
            $createdFiles = $this->createFiles($response, $files);
            $response->files()->saveMany($createdFiles);
        }

        $responseContent = fake()->paragraph();

        return function () use ($response, $responseContent, $saveResponse) {
            yield $responseContent;

            $response->content .= $responseContent;

            $saveResponse($response);
        };
    }

    public function getMaxAssistantInstructionsLength(): int
    {
        return 30000;
    }

    public function getDeployment(): ?string
    {
        return null;
    }

    public function supportsMessageFileUploads(): bool
    {
        return true;
    }

    public function supportsAssistantFileUploads(): bool
    {
        return true;
    }

    protected function createFiles(AiMessage $message, array $files): Collection
    {
        return collect($files)->map(function (string $file): AiMessageFile {
            $fileRecord = new AiMessageFile();
            $fileRecord->temporary_url = 'temp-url';
            $fileRecord->name = 'test';
            $fileRecord->mime_type = 'text/plain';
            $fileRecord->file_id = Str::random(12);

            return $fileRecord;
        });
    }
}
