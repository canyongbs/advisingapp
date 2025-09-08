<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QnaAdvisorMessage extends BaseModel
{
    public $fillable = [
        'message_id',
        'content',
        'context',
        'request',
        'next_request_options',
        'thread_id',
        'author_type',
        'author_id',
    ];

    protected $casts = [
        'next_request_options' => 'array',
        'request' => 'encrypted:array',
    ];

    /**
     * @return BelongsTo<QnaAdvisorThread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisorThread::class, 'thread_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo('author');
    }
}
