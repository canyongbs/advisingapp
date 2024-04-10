<?php

namespace AdvisingApp\Timeline\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class History extends BaseModel
{
    use SoftDeletes;

    protected $table = 'histories';

    protected $fillable = [
        'event',
        'old',
        'new',
    ];

    protected $casts = [
        'old' => 'array',
        'new' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    abstract public function formatted(): Attribute;
}
