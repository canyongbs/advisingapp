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

namespace AdvisingApp\Ai\Jobs;

use AdvisingApp\Ai\Actions\GetQnaAdvisorInstructions;
use AdvisingApp\Ai\Events\AdvisorMessageChunk;
use AdvisingApp\Ai\Events\AdvisorNextRequestOptions;
use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SendAdvisorMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        protected string $chatId,
        protected QnaAdvisor $advisor,
        protected string $content,
        protected array $options = [],
    ) {}

    public function handle(GetQnaAdvisorInstructions $getQnaAdvisorInstructions): void
    {
        try {
            $aiService = $this->advisor->model->getService();

            $stream = $aiService->streamRaw(
                $getQnaAdvisorInstructions->execute($this->advisor),
                $this->content,
                shouldTrack: false,
                options: $this->options,
            );

            $chunkBuffer = [];
            $chunkCount = 0;

            foreach ($stream() as $chunk) {
                if ($chunk['type'] === 'next_request_options') {
                    event(new AdvisorNextRequestOptions(
                        $this->chatId,
                        $chunk['options'],
                    ));

                    continue;
                }

                if ($chunk['type'] === 'text') {
                    $chunkBuffer[] = $chunk['content'];
                    $chunkCount++;

                    if ($chunkCount >= 30) {
                        event(new AdvisorMessageChunk(
                            $this->chatId,
                            content: implode('', $chunkBuffer),
                        ));

                        $chunkBuffer = [];
                        $chunkCount = 0;
                    }
                }
            }

            if (! empty($chunkBuffer)) {
                event(new AdvisorMessageChunk(
                    $this->chatId,
                    content: implode('', $chunkBuffer),
                ));
            }

            event(new AdvisorMessageChunk(
                $this->chatId,
                content: '',
                isComplete: true,
            ));
        } catch (Throwable $exception) {
            report($exception);

            event(new AdvisorMessageChunk(
                $this->chatId,
                content: '',
                isComplete: false,
                error: 'An error happened when sending your message.',
            ));
        }
    }
}
