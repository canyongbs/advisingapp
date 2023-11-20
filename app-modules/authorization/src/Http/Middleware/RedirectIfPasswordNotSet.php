<?php

namespace Assist\Authorization\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfPasswordNotSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->is_external) {
            return $next($request);
        }

        if (blank($user->password)) {
            return redirect()->route('filament.admin.auth.set-password');
        }

        return $next($request);
    }
}
