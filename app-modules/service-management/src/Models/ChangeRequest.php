<?php

namespace AdvisingApp\ServiceManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class ChangeRequest extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
        'backout_strategy',
        'change_request_status_id',
        'change_request_type_id',
        'created_by',
        'description',
        'end_time',
        'impact',
        'likelihood',
        'reason',
        'start_time',
        'title',
    ];

    protected $casts = [
        'end_time' => 'datetime',
        'impact' => 'integer',
        'likelihood' => 'integer',
        'risk_score' => 'integer',
        'start_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestType::class, 'change_request_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestStatus::class, 'change_request_status_id');
    }

    public static function getColorBasedOnRisk(?int $value): string
    {
        $classMap = [
            '1-4' => 'green',
            '5-10' => 'yellow',
            '11-16' => 'orange',
            '17-25' => 'red',
        ];

        foreach ($classMap as $range => $classes) {
            [$min, $max] = explode('-', $range);

            if ($value >= (int) $min && $value <= (int) $max) {
                return $classes;
            }
        }

        return '';
    }
}
