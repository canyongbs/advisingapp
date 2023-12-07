<?php

namespace Assist\Auditing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Assist\Auditing\Contracts\Auditable;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Auditable, Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    use \Assist\Auditing\Auditable;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_admin' => 'bool',
    ];

    /**
     * Uppercase first name character accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getFirstNameAttribute(string $value): string
    {
        return ucfirst($value);
    }
}
