<?php

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\CaseManagement\Database\Factories\CaseTypeAuditorFactory;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CaseTypeAuditor extends Pivot
{
    /** @use HasFactory<CaseTypeAuditorFactory> */
    use HasFactory;

    protected $table = 'case_type_auditors';

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
