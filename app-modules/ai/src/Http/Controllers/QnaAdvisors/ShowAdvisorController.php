<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ShowAdvisorController
{
    public function __invoke(QnaAdvisor $advisor): JsonResponse
    {
        $chatId = Str::random(32);

        return response()->json([
            'chat_id' => $chatId,
            'send_message_url' => URL::to(
                URL::temporarySignedRoute(
                    name: 'ai.qna-advisors.messages.send',
                    expiration: now()->addHours(24),
                    parameters: ['advisor' => $advisor, 'chat_id' => $chatId],
                    absolute: false,
                ),
            ),
            'websockets_config' => config('filament.broadcasting.echo'),
        ]);
    }
}
