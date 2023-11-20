<?php

namespace Assist\ServiceManagement\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Timeline\Timelines\ServiceRequestUpdateTimeline;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @mixin IdeHelperServiceRequestUpdate
 */
class ServiceRequestUpdate extends BaseModel implements Auditable, CanTriggerAutoSubscription, ProvidesATimeline
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'service_request_id',
        'update',
        'internal',
        'direction',
    ];

    protected $casts = [
        'internal' => 'boolean',
        'direction' => ServiceRequestUpdateDirection::class,
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function getSubscribable(): ?Subscribable
    {
        /** @var Subscribable|Model $respondent */
        $respondent = $this->serviceRequest->respondent;

        return $respondent instanceof Subscribable
            ? $respondent
            : null;
    }

    public function timeline(): ServiceRequestUpdateTimeline
    {
        return new ServiceRequestUpdateTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->serviceRequestUpdates()->get();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
