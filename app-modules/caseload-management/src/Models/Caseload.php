<?php

namespace Assist\CaseloadManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperCaseload
 */
class Caseload extends BaseModel
{
    protected $fillable = [
        'query',
        'filters',
        'name',
        'model',
        'type',
    ];

    protected $casts = [
        'filters' => 'array',
        'model' => CaseloadModel::class,
        'type' => CaseloadType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(CaseloadSubject::class);
    }
}
