<?php

namespace AdvisingApp\Analytics\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategoryClassification;

class AnalyticsResourceCategory extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
        'classification',
    ];

    protected $casts = [
        'classification' => AnalyticsResourceCategoryClassification::class,
    ];

    public function resources(): HasMany
    {
        return $this->hasMany(AnalyticsResource::class, 'category_id');
    }
}
