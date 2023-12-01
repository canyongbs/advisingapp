<?php

namespace App\Support;

use Illuminate\Auth\Access\Response;

class FeatureAccessResponse extends Response
{
    protected $message = 'Feature Access Denied';

    public static function deny($message = null, $code = null): FeatureAccessResponse|static
    {
        return new static(false, $message, $code);
    }

    public static function allow($message = null, $code = null): FeatureAccessResponse|static
    {
        return new static(true, $message, $code);
    }
}
