<?php

namespace Assist\Auditing\Resolvers;

use Assist\Auditing\Contracts\Auditable;
use Assist\Auditing\Contracts\Resolver;

class DumpResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return '';
    }
}
