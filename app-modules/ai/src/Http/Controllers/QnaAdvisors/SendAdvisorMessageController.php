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

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Actions\GetQnaAdvisorInstructions;
use AdvisingApp\Ai\Jobs\QnaAdvisors\SendQnaAdvisorMessage;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorThread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SendAdvisorMessageController
{
    public function __invoke(Request $request, GetQnaAdvisorInstructions $getQnaAdvisorInstructions, QnaAdvisor $advisor): StreamedResponse | JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:25000'],
            'thread_id' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
        ]);

        if ($request->query('preview')) {
            $aiService = $advisor->model->getService();

            try {
                return new StreamedResponse(
                    $aiService->stream(
                        prompt: $getQnaAdvisorInstructions->execute($advisor),
                        content: $data['content'],
                        files: [
                            ...$advisor->files()->whereNotNull('parsing_results')->get()->all(),
                            ...$advisor->links()->whereNotNull('parsing_results')->get()->all(),
                        ],
                        shouldTrack: false,
                        options: $data['options'] ?? [],
                    ),
                    headers: [
                        'Content-Type' => 'text/html; charset=utf-8;',
                        'Cache-Control' => 'no-cache',
                        'X-Accel-Buffering' => 'no',
                    ],
                );
            } catch (Throwable $exception) {
                report($exception);

                return response()->json([
                    'message' => 'An error happened when sending your message.',
                ], 503);
            }
        }

        $author = auth('student')->user() ?? auth('prospect')->user();

        if (filled($data['thread_id'] ?? null)) {
            $thread = QnaAdvisorThread::query()
                ->whereKey($data['thread_id'])
                ->whereBelongsTo($advisor, 'advisor')
                ->whereMorphedTo('author', $author)
                ->firstOrFail();
        } else {
            $thread = new QnaAdvisorThread();
            $thread->advisor()->associate($advisor);
            $thread->author()->associate($author);
            $thread->save();
        }

        dispatch(new SendQnaAdvisorMessage(
            $advisor,
            $thread,
            $data['content'],
            request: [
                'headers' => Arr::only(
                    request()->headers->all(),
                    ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                ),
                'ip' => request()->ip(),
            ],
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
        ]);
    }
}
