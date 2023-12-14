<?php

namespace AdvisingApp\Application\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;

/**
 * @mixin IdeHelperApplicationSubmissionState
 */
class ApplicationSubmissionState extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'classification',
        'name',
        'color',
        'description',
    ];

    protected $casts = [
        'classification' => ApplicationSubmissionStateClassification::class,
        'color' => ApplicationSubmissionStateColorOptions::class,
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(ApplicationSubmission::class, 'state_id');
    }
}
