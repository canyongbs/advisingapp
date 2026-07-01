<?php

namespace AdvisingApp\Report\Models;

use AdvisingApp\Team\Models\Team;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportTeamAccess extends BaseModel
{
    protected $fillable = [
        'report_key',
        'team_id',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
