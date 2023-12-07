<?php

namespace Assist\Auditing\Contracts;

interface UserResolver
{
    /**
     * Resolve the User.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve(Auditable $auditable);
}
