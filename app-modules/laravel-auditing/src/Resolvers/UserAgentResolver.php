<?php

namespace Assist\LaravelAuditing\Resolvers;

use Illuminate\Support\Facades\Request;
use Assist\LaravelAuditing\Contracts\Resolver;
use Assist\LaravelAuditing\Contracts\Auditable;

class UserAgentResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return $auditable->preloadedResolverData['user_agent'] ?? Request::header('User-Agent');
    }
}
