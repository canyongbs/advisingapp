<?php

namespace AdvisingApp\Ai\Models;

use App\Models\User;
use App\Models\BaseModel;
use AdvisingApp\Ai\Enums\AiApplication;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiThreadFolder extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'application',
        'user_id',
    ];

    protected $casts = [
        'application' => AiApplication::class,
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(AiThread::class, 'folder_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
