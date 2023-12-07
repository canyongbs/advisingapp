<?php

namespace Assist\Auditing\Resolvers;

use Assist\Auditing\Contracts\Resolver;
use Illuminate\Support\Facades\Request;
use Assist\Auditing\Contracts\Auditable;

class UserAgentResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return $auditable->preloadedResolverData['user_agent'] ?? Request::header('User-Agent');
    }
}
