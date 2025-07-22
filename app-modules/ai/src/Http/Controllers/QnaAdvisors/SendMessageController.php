<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use AppHttp\Controllers\Controller;
use AdvisingApp\Ai\Models\QnaAdvisor;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SendMessageController
{
    public function __invoke(QnaAdvisor $advisor): StreamedResponse | JsonResponse
    {
        return response()->json([
            'message' => 'Message sent successfully to the Q&A advisor.',
        ]);
    }
}
