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

namespace AdvisingApp\Ai\Actions;

use AdvisingApp\Ai\Exceptions\AiAssistantArchivedException;
use AdvisingApp\Ai\Exceptions\AiResponseToCompleteDoesNotExistException;
use AdvisingApp\Ai\Exceptions\AiThreadLockedException;
use AdvisingApp\Ai\Models\AiMessage;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordTrackedEvent;
use Closure;

class CompleteResponse
{
    public function __invoke(AiThread $thread): Closure
    {
        if ($thread->locked_at) {
            throw new AiThreadLockedException();
        }

        if ($thread->assistant->archived_at) {
            throw new AiAssistantArchivedException();
        }

        $response = $thread->messages()
            ->whereNull('user_id')
            ->latest()
            ->first();

        if (! $response) {
            throw new AiResponseToCompleteDoesNotExistException();
        }

        if (str($response->content)->endsWith('...')) {
            $response->content = (string) str($response->content)
                ->beforeLast('...')
                ->append(' ');
        }

        $aiService = $thread->assistant->model->getService();

        $aiService->ensureAssistantAndThreadExists($thread);

        return $aiService
            ->completeResponse(
                response: $response,
                files: $thread->messages()
                    ->whereNotNull('user_id')
                    ->latest()
                    ->first()
                    ?->files?->all() ?? [],
                saveResponse: function (AiMessage $response) use ($thread) {
                    $response->save();

                    dispatch(new RecordTrackedEvent(
                        type: TrackedEventType::AiExchange,
                        occurredAt: now(),
                    ));

                    $thread->touch();
                },
            );
    }
}
