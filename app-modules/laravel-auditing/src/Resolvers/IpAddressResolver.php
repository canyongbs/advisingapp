<?php

namespace Assist\Auditing\Resolvers;

use Illuminate\Support\Facades\Request;
use Assist\Auditing\Contracts\Auditable;
use Assist\Auditing\Contracts\Resolver;

class IpAddressResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return $auditable->preloadedResolverData['ip_address'] ?? Request::ip();
    }
}
