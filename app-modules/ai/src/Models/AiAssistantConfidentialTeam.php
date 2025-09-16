<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Database\Factories\AiAssistantConfidentialTeamFactory;
use AdvisingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AiAssistantConfidentialTeam extends Pivot
{
    /** @use HasFactory<AiAssistantConfidentialTeamFactory> */
    use HasFactory;

    use HasUuids;

    public function assistant()
    {
        return $this->belongsTo(AiAssistant::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
