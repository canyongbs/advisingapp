<?php

namespace Assist\Auditing\Resolvers;

use Assist\Auditing\Contracts\Resolver;
use Illuminate\Support\Facades\Request;
use Assist\Auditing\Contracts\Auditable;

class IpAddressResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return $auditable->preloadedResolverData['ip_address'] ?? Request::ip();
    }
}
