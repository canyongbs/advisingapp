<?php

namespace Assist\Auditing\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Assist\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleExcludes extends Model implements Auditable
{
    use \Assist\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'articles';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'reviewed' => 'bool',
        'config' => 'json',
    ];

    public $auditExclude = ['title'];

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
        'title',
        'content',
        'published_at',
        'reviewed',
    ];

    public function __construct(array $attributes = [])
    {
        if (class_exists(\Illuminate\Database\Eloquent\Casts\AsArrayObject::class)) {
            $this->casts['config'] = \Illuminate\Database\Eloquent\Casts\AsArrayObject::class;
        }
        parent::__construct($attributes);
    }

    /**
     * Uppercase Title accessor.
     *
     * @param string $value
     *
     * @return string
     */
    public function getTitleAttribute(string $value): string
    {
        return strtoupper($value);
    }
}
