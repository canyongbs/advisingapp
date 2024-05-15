<?php

namespace AdvisingApp\Ai\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AiThread extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'thread_id',
        'name',
        'assistant_id',
        'folder_id',
        'user_id',
    ];

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(AiThreadFolder::class, 'folder_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'thread_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            table: 'ai_messages',
            foreignPivotKey: 'thread_id',
        )->using(AiMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
