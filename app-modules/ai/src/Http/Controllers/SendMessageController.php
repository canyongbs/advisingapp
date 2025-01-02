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

namespace AdvisingApp\Ai\Http\Controllers;

use AdvisingApp\Ai\Actions\SendMessage;
use AdvisingApp\Ai\Exceptions\AiAssistantArchivedException;
use AdvisingApp\Ai\Exceptions\AiThreadLockedException;
use AdvisingApp\Ai\Http\Requests\SendMessageRequest;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SendMessageController
{
    public function __invoke(SendMessageRequest $request, AiThread $thread): StreamedResponse | JsonResponse
    {
        try {
            return response()->stream(
                app(SendMessage::class)(
                    $thread,
                    $request->validated('prompt_id') ? Prompt::find($request->validated('prompt_id')) : $request->validated('content'),
                    $request->validated('files'),
                ),
                headers: [
                    'Content-Type' => 'text/html; charset=utf-8;',
                    'Cache-Control' => 'no-cache',
                    'X-Accel-Buffering' => 'no',
                ],
            );
        } catch (AiAssistantArchivedException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        } catch (AiThreadLockedException $exception) {
            return response()->json([
                'isThreadLocked' => true,
                'message' => $exception->getMessage(),
            ], 503);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'An error happened when sending your message.',
            ], 503);
        }
    }
}
