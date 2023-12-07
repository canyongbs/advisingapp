<?php

namespace Assist\LaravelAuditing\Contracts;

/**
 * @deprecated
 * @see Resolver
 */
interface UrlResolver
{
    /**
     * Resolve the URL.
     *
     * @return string
     */
    public static function resolve(): string;
}
