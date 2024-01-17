<?php

namespace AdvisingApp\ServiceManagement\Models;

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

    public function type(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestType::class, 'change_request_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestStatus::class, 'change_request_status_id');
    }

    public static function getClassesBasedOnRisk(int $value): string
    {
        $classMap = [
            '1-4' => 'border-green-500 bg-green-400/10 text-green-500 ring-green-500 dark:border-green-500 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-500',
            '5-10' => 'border-yellow-500 bg-yellow-400/10 text-yellow-500 ring-yellow-500 dark:border-yellow-500 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-500',
            '11-16' => 'border-orange-500 bg-orange-400/10 text-orange-500 ring-orange-500 dark:border-orange-500 dark:bg-orange-400/10 dark:text-orange-500 dark:ring-orange-500',
            '17-25' => 'border-red-600 bg-red-400/10 text-red-600 ring-red-600 dark:border-red-600 dark:bg-red-400/10 dark:text-red-600 dark:ring-red-600',
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
