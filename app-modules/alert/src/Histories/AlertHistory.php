<?php

namespace AdvisingApp\Alert\Histories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Alert\Enums\AlertStatus;
use AdvisingApp\Timeline\Models\History;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Casts\Attribute;
use AdvisingApp\Timeline\Timelines\AlertHistoryTimeline;
use AdvisingApp\Timeline\Models\Contracts\ProvidesATimeline;

class AlertHistory extends History implements ProvidesATimeline
{
    public function timeline(): AlertHistoryTimeline
    {
        return new AlertHistoryTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        /* @var Student|Prospect $forModel */
        return $forModel->alertHistories()->get();
    }

    public function formatted(): Attribute
    {
        return Attribute::get(
            fn () => collect($this->new)
                ->map(function ($value, $key) {
                    return match ($key) {
                        'status' => [
                            'key' => 'Status',
                            'old' => array_key_exists($key, $this->old) ? AlertStatus::tryFrom($this->old[$key])?->getLabel() : null,
                            'new' => AlertStatus::tryFrom($value)?->getLabel(),
                        ],
                        'severity' => [
                            'key' => 'Severity',
                            'old' => array_key_exists($key, $this->old) ? AlertSeverity::tryFrom($this->old[$key])?->getLabel() : null,
                            'new' => AlertSeverity::tryFrom($value)?->getLabel(),
                        ],
                        default => [
                            'key' => str($key)->headline()->toString(),
                            'old' => $this->old[$key] ?? null,
                            'new' => $value,
                        ],
                    };
                })
                ->filter()
        );
    }
}
