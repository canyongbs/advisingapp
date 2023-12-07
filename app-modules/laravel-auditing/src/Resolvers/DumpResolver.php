<?php

namespace Assist\Auditing\Resolvers;

use Assist\Auditing\Contracts\Resolver;
use Assist\Auditing\Contracts\Auditable;

class DumpResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return '';
    }
}
