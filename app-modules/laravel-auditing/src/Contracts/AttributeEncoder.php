<?php

namespace Assist\LaravelAuditing\Contracts;

interface AttributeEncoder extends AttributeModifier
{
    /**
     * Encode an attribute value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function encode($value);

    /**
     * Decode an attribute value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function decode($value);
}
