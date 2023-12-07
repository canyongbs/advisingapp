<?php

namespace Assist\LaravelAuditing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Assist\LaravelAuditing\Contracts\Auditable;

class User extends Model implements Auditable, Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    use \Assist\LaravelAuditing\Auditable;

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
