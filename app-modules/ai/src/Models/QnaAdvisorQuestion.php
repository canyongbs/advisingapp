<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperQnaAdvisorQuestion
 */
class QnaAdvisorQuestion extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = ['question', 'answer', 'category_id'];

    /**
     * @return BelongsTo<QnaAdvisorCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisorCategory::class, 'category_id');
    }
}
