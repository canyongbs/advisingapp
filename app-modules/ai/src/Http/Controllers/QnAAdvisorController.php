<?php

namespace AdvisingApp\Ai\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QnAAdvisorController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'QnA Advisor widget endpoint',
            'status' => 'success',
        ]);
    }
}
