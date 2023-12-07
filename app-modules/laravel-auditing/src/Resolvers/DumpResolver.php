<?php

namespace Assist\LaravelAuditing\Resolvers;

use Assist\LaravelAuditing\Contracts\Resolver;
use Assist\LaravelAuditing\Contracts\Auditable;

class DumpResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return '';
    }
}
