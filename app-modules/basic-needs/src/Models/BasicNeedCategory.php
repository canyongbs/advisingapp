<?php

namespace AdvisingApp\BasicNeeds\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedCategoryResource;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BasicNeedCategory extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [];

    public function basicNeedProgram(): HasMany
    {
        return $this->hasMany(BasicNeedProgram::class, 'basic_need_category_id');
    }

    public static function filamentResource(): string
    {
        return BasicNeedCategoryResource::class;
    }
}
