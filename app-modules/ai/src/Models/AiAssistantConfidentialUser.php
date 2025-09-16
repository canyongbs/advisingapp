<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Database\Factories\AiAssistantConfidentialUserFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AiAssistantConfidentialUser extends Pivot
{
    /** @use HasFactory<AiAssistantConfidentialUserFactory> */
    use HasFactory;

    use HasUuids;

    public function assistant()
    {
        return $this->belongsTo(AiAssistant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
