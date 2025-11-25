<?php

namespace AdvisingApp\StudentDataModel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperHold
 */
class Hold extends Model
{
    use SoftDeletes;
    use UsesTenantConnection;

    protected $fillable = [
        'sisid',
        'hold_id',
        'name',
        'category',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'sisid', 'sisid');
    }
}
