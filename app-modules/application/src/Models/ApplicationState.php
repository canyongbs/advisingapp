<?php

namespace AdvisingApp\Application\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AdvisingApp\Application\Enums\ApplicationStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationStateClassification;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ApplicationState extends BaseModel implements Auditable
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
        'classification' => ApplicationStateClassification::class,
        'color' => ApplicationStateColorOptions::class,
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'state_id');
    }
}
