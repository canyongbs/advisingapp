<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Filament\Actions\ViewAction;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Engagement\Models\Contracts\Timelineable;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Models\Contracts\RendersCustomTimelineView;
use Assist\Engagement\Filament\Pages\Components\EngagementResponseViewAction;

/**
 * @mixin IdeHelperEngagementResponse
 */
class EngagementResponse extends BaseModel implements Auditable, Timelineable, RendersCustomTimelineView
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
        return true;
    }

    public function renderCustomView(): string
    {
        return 'engagement::engagement-response-timeline-item';
    }

    public function modalViewAction(): ViewAction
    {
        return EngagementResponseViewAction::make()->record($this);
    }

    public static function getTimeline(Model $forModel): Collection
    {
        return $forModel->engagementResponses()->get();
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
