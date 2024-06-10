<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiMessageFile extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'file_id',
        'message_id',
        'mime_type',
        'name',
        'temporary_url',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(AiMessage::class, 'message_id');
    }
}
