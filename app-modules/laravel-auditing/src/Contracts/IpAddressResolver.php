<?php

namespace Assist\LaravelAuditing\Contracts;

/**
 * @deprecated
 * @see Resolver
 */
interface IpAddressResolver
{
    /**
     * Resolve the IP Address.
     *
     * @return string
     */
    public static function resolve(): string;
}
