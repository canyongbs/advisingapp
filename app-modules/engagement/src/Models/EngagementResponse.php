<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Timeline\Timelines\EngagementResponseTimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperEngagementResponse
 */
class EngagementResponse extends BaseModel implements Auditable, ProvidesATimeline
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

    public function timeline(): EngagementResponseTimeline
    {
        return new EngagementResponseTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagementResponses()->get();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo(
            name: 'sender',
            type: 'sender_type',
            id: 'sender_id',
        );
    }

    public function scopeSentByStudent(Builder $query): void
    {
        $query->where('sender_type', resolve(Student::class)->getMorphClass());
    }

    public function scopeSentByProspect(Builder $query): void
    {
        $query->where('sender_type', resolve(Prospect::class)->getMorphClass());
    }
}
