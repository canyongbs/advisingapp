<?php

namespace Assist\LaravelAuditing\Contracts;

interface Resolver
{
    public static function resolve(Auditable $auditable);
}
