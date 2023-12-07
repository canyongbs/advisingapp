<?php

namespace Assist\Auditing\Tests\fixtures;

use Assist\Auditing\Contracts\Resolver;
use Assist\Auditing\Contracts\Auditable;

class TenantResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        return 1;
    }
}
