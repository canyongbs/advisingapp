<?php

namespace App\Support;

use Illuminate\Auth\Access\Response;

class FeatureAccessResponse extends Response
{
    protected $message = 'Feature Access Denied';

    final public function __construct($allowed, $message = '', $code = null)
    {
        parent::__construct($allowed, $message, $code);
    }

    public static function deny($message = null, $code = null): FeatureAccessResponse|static
    {
        return new static(false, $message, $code);
    }

    public static function allow($message = null, $code = null): FeatureAccessResponse|static
    {
        return new static(true, $message, $code);
    }
}
