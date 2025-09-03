<?php

namespace AdvisingApp\Ai\Http\Middleware;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class QnaAdvisorAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $advisor = $request->route('advisor');

        if (! $advisor instanceof QnaAdvisor) {
            return response()->json(status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (! $advisor->is_requires_authentication_enabled) {
            // This Advisor does not require an Educatable to be authorized. Allow request.
            return $next($request);
        }

        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Bearer token missing'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            return response()->json(['error' => 'Invalid bearer token'], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['error' => 'Bearer token expired'], 401);
        }

        $educatable = $accessToken->tokenable;

        match ($educatable::class) {
            Student::class => Auth::guard('student')->onceUsingId($educatable->getKey()),
            Prospect::class => Auth::guard('prospect')->onceUsingId($educatable->getKey()),
            default => throw new Exception('Unable to resolve to proper entity.')
        };

        return $next($request);
    }
}
