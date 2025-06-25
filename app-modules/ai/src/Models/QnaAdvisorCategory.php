<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperQnaAdvisorCategory
 */
class QnaAdvisorCategory extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
        'qna_advisor_id',
    ];

    /**
     * @return BelongsTo<QnaAdvisor, $this>
     */
    public function qnaAdvisor(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisor::class, 'qna_advisor_id');
    }

    /**
     * @return HasMany<QnaAdvisorQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QnaAdvisorQuestion::class, 'category_id');
    }
}
