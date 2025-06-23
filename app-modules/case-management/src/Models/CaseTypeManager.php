<?php

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\CaseManagement\Database\Factories\CaseTypeManagerFactory;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CaseTypeManager extends Pivot
{
    /** @use HasFactory<CaseTypeManagerFactory> */
    use HasFactory;

    protected $table = 'case_type_managers';

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<CaseType, $this>
     */
    public function caseType(): BelongsTo
    {
        return $this->belongsTo(CaseType::class);
    }
}
