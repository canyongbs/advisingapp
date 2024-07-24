<?php

namespace App\AuditResolvers;

use OwenIt\Auditing\Contracts\Resolver;
use OwenIt\Auditing\Contracts\Auditable;

class ChangeAgentNameResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return $auditable->preloadedResolverData['change_agent_name'] ?? call_user_func([config('audit.user.resolver'), 'resolve'])?->name;
    }
}
