<?php

namespace Assist\Auditing\Resolvers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Assist\Auditing\Contracts\Auditable;

class UserResolver implements \Assist\Auditing\Contracts\UserResolver
{
    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve(Auditable $auditable)
    {
        if (! empty($auditable->preloadedResolverData['user'])) {
            return $auditable->preloadedResolverData['user'];
        }

        $guards = Config::get('audit.user.guards', [
            \config('auth.defaults.guard')
        ]);

        foreach ($guards as $guard) {
            try {
                $authenticated = Auth::guard($guard)->check();
            } catch (\Exception $exception) {
                continue;
            }

            if (true === $authenticated) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }
}
