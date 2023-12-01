<?php

namespace Assist\ServiceManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Timeline\Timelines\ServiceRequestAssignmentTimeline;
use Assist\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

class ServiceRequestAssignment extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline
{
    use HasUuids;
    use AuditableTrait;

    protected $casts = [
        'assigned_at' => 'datetime',
        'status' => ServiceRequestAssignmentStatus::class,
    ];

    protected $fillable = [
        'user_id',
        'assigned_at',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function timeline(): ServiceRequestAssignmentTimeline
    {
        return new ServiceRequestAssignmentTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->assignments()->get();
    }

    public function getSubscribable(): ?Subscribable
    {
        /** @var Subscribable|Model $respondent */
        $respondent = $this->serviceRequest->respondent;

        return $respondent instanceof Subscribable
            ? $respondent
            : null;
    }
}
