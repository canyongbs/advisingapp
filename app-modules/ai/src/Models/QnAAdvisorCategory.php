<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperQnAAdvisorCategory
 */
class QnAAdvisorCategory extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'qn_a_advisor_id',
    ];

    /**
     * @return BelongsTo<QnAAdvisor, $this>
     */
    public function qnAAdvisor(): BelongsTo
    {
        return $this->belongsTo(QnAAdvisor::class, 'qn_a_advisor_id');
    }

    /**
     * @return HasMany<QnAAdvisorQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QnAAdvisorQuestion::class, 'category_id');
    }
}
