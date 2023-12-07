<?php

namespace Assist\Auditing\Encoders;

class Base64Encoder implements \Assist\Auditing\Contracts\AttributeEncoder
{
    /**
     * {@inheritdoc}
     */
    public static function encode($value)
    {
        return base64_encode($value);
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($value)
    {
        return base64_decode($value);
    }
}
