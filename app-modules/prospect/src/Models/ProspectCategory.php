<?php

namespace AdvisingApp\Prospect\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Prospect\Filament\Resources\ProspectCategoryResource;

class ProspectCategory extends Model implements Auditable
{
    use HasFactory;
    use AuditableTrait;
    use SoftDeletes;
    use HasUuids;

    protected $guarded = [];

    public function prospectProgram(): HasMany
    {
        return $this->hasMany(ProspectProgram::class, 'prospect_category_id');
    }

    public static function filamentResource(): string
    {
        return ProspectCategoryResource::class;
    }
}
