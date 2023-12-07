<?php

namespace Assist\Auditing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Assist\Auditing\Contracts\Auditable;

class ApiModel extends Model implements Auditable
{
    use \Assist\Auditing\Auditable;
    use SoftDeletes;

    /**
     * @var string UUID key
     */
    public $primaryKey = 'api_model_id';

    /**
     * @var bool Set to false for UUID keys
     */
    public $incrementing = false;

    /**
     * @var string Set to string for UUID keys
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'published_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'api_model_id',
        'content',
        'published_at',
    ];
}
