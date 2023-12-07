<?php

namespace Assist\Auditing\Contracts;

interface Resolver
{
    public static function resolve(Auditable $auditable);
}
