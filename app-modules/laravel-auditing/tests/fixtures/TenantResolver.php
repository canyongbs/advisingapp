<?php

namespace Assist\LaravelAuditing\Tests\fixtures;

use Assist\LaravelAuditing\Contracts\Resolver;
use Assist\LaravelAuditing\Contracts\Auditable;

class TenantResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return 1;
    }
}
