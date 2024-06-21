<?php

namespace AdvisingApp\BasicNeeds\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class BasicNeedsProgram extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [];

    public function basicNeedsCategories(): BelongsTo
    {
        return $this->belongsTo(BasicNeedsCategory::class, 'basic_need_category_id', 'id');
    }
}
