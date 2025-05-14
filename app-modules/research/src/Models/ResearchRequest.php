<?php

namespace AdvisingApp\Research\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ResearchRequestFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResearchRequest extends BaseModel
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
     * @return HasMany<ResearchRequestQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(ResearchRequestQuestion::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
