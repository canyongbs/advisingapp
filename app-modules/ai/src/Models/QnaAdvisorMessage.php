<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QnaAdvisorMessage extends BaseModel
{
    /**
     * @return BelongsTo<QnaAdvisorThread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisorThread::class, 'thread_id');
    }
}
