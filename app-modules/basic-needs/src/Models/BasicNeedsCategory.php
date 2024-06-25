<?php

namespace AdvisingApp\BasicNeeds\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class BasicNeedsCategory extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
    ];

    public function basicNeedsProgram(): HasMany
    {
        return $this->hasMany(BasicNeedsProgram::class, 'basic_needs_category_id');
    }
}
