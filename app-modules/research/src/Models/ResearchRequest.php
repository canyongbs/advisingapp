<?php

namespace AdvisingApp\Research\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ResearchRequestFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResearchRequest extends Model
{
    /** @use HasFactory<ResearchRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'topic',
        'results',
        'user_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
