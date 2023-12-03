<?php

namespace Assist\ServiceManagement\Models;

use Exception;
use App\Models\BaseModel;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;
use Assist\Timeline\Timelines\ServiceRequestHistoryTimeline;

class ServiceRequestHistory extends BaseModel implements ProvidesATimeline
{
    protected $casts = [
        'original_values' => 'array',
        'new_values' => 'array',
    ];

    protected $fillable = [
        'original_values',
        'new_values',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function timeline(): ServiceRequestHistoryTimeline
    {
        return new ServiceRequestHistoryTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->histories()->get();
    }

    public function getUpdates(): array
    {
        $updates = [];

        foreach ($this->new_values as $key => $value) {
            $updates[] = [
                'key' => $key,
                'old' => $this->original_values[$key],
                'new' => $value,
            ];
        }

        return $updates;
    }

    protected function newValuesFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatValues(json_decode($attributes['new_values'], true)),
        );
    }

    protected function originalValuesFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatValues(json_decode($attributes['original_values'], true)),
        );
    }

    protected function formatValues(array $value): array
    {
        $relationsMap = [
            'priority_id' => [ServiceRequestPriority::class, 'name'],
            'status_id' => [ServiceRequestStatus::class, 'name'],
            'division_id' => [Division::class, 'name'],
            'type_id' => [ServiceRequestType::class, 'name'],
            'respondent_id' => [
                [Prospect::class, Student::class],
            ],
        ];

        foreach ($value as $key => $data) {
            $readableKey = $this->transformReadableKey($key);

            $value[$readableKey] = $value[$key];

            if (array_key_exists($key, $relationsMap)) {
                if (is_array($relationsMap[$key][0])) {
                    foreach ($relationsMap[$key][0] as $educatableClass) {
                        $found = null;

                        // This is to overcome an issue that comes from an incorrect type when trying to find a prospect or student with the wrong data type
                        try {
                            $found = $educatableClass::find($value[$key]);
                        } catch (Exception $e) {
                            // TODO We might want to do *something* here...
                        }

                        if (! is_null($found)) {
                            $value[$readableKey] = $found->{$educatableClass::displayNameKey()};
                        }
                    }
                } else {
                    $value[$readableKey] = $relationsMap[$key][0]::find($value[$key])->{$relationsMap[$key][1]};
                }
            }

            unset($value[$key]);
        }

        return $value;
    }

    protected function transformReadableKey(string $key): string
    {
        if (Str::endsWith($key, '_id')) {
            $key = Str::replaceLast('_id', '', $key);
        }

        return Str::of($key)->replace('_', ' ')->title()->toString();
    }
}
