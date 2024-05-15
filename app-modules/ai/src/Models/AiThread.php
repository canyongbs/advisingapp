<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Enums\AiModel;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
