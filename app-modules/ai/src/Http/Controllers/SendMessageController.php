<?php

namespace AdvisingApp\Ai\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Actions\SendMessage;
use AdvisingApp\Ai\Exceptions\MessageResponseTimeoutException;

class SendMessageController
{
    public function __invoke(AiThread $thread, Request $request): JsonResponse
    {
        if (! $thread->user()->is(auth()->user())) {
            abort(404);
        }

        $content = $request->validate([
            'content' => ['required', 'max:1000'],
        ])['content'];

        try {
            $responseContent = app(SendMessage::class)($thread, $content);
        } catch (MessageResponseTimeoutException $exception) {
            return response()->json([
                'message' => 'The assistant is taking too long to respond. Please try again later.',
            ], 408);
        }

        return response()->json([
            'content' => $responseContent,
        ]);
    }
}
