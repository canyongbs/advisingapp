<?php

namespace Assist\Auditing\Tests\Casts;

use Assist\Auditing\Tests\Models\Money as MoneyValueObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Money implements CastsAttributes
{
    /**
     * {@inheritdoc}
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new MoneyValueObject($value, 'USD');
    }

    /**
     * {@inheritdoc}
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return $value;
    }
}
