<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Engagement\Models\Contracts\Timelineable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperEngagementResponse
 */
class EngagementResponse extends BaseModel implements Auditable, Timelineable
{
    use AuditableTrait;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'content',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function icon(): string
    {
        return 'heroicon-o-arrow-small-left';
    }

    public function sortableBy(): string
    {
        return $this->sent_at;
    }

    public function providesCustomView(): bool
    {
        return false;
    }

    public function sender(): MorphTo
    {
        return $this->morphTo(
            name: 'sender',
            type: 'sender_type',
            id: 'sender_id',
        );
    }
}
