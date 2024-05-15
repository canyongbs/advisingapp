<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiThreadFolder extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
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
