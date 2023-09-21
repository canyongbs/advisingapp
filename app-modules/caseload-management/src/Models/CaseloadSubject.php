<?php

namespace Assist\CaseloadManagement\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseloadSubject extends BaseModel
{
    public function caseload(): BelongsTo
    {
        return $this->belongsTo(Caseload::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
