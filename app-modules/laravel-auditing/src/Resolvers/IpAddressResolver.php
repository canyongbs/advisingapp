<?php

namespace Assist\LaravelAuditing\Resolvers;

use Illuminate\Support\Facades\Request;
use Assist\LaravelAuditing\Contracts\Resolver;
use Assist\LaravelAuditing\Contracts\Auditable;

class IpAddressResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return $auditable->preloadedResolverData['ip_address'] ?? Request::ip();
    }
}
