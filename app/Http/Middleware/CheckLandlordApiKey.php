<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckLandlordApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Hash::check($request->bearerToken(), base64_decode(config('app.landlord_api_key')))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid API key',
                ], 403);
            }

            abort(403, 'Invalid API key');
        }

        return $next($request);
    }
}
