<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

class ShowAdvisorController
{
    public function __invoke(QnaAdvisor $advisor): JsonResponse
    {
        return response()->json([
            'send_message_url' => URL::to(
                URL::signedRoute(
                    name: 'ai.qna-advisors.messages.send',
                    parameters: ['advisor' => $advisor],
                    absolute: false,
                ),
            ),
        ]);
    }
}
