<?php

namespace AdvisingApp\Timeline\Models\Concerns;

use Laravel\Pennant\Feature;
use Illuminate\Support\Collection;
use AdvisingApp\Timeline\Observers\HistorySubjectObserver;

trait InteractsWithHistory
{
    protected array $ignoredAttributes = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function bootInteractsWithHistory(): void
    {
        static::observe(HistorySubjectObserver::class);
    }

    public function processCustomHistories(string $event, Collection $old, Collection $new, Collection $pending): void {}

    public function processHistory(string $event, Collection $old, Collection $new): void
    {
        $pending = collect();

        $this->processCustomHistories($event, $old, $new, $pending);

        $keys = $new->keys()->diff($this->ignoredAttributes);

        $this->recordHistory($event, $old->only($keys), $new->only($keys), $pending);

        if (Feature::active('educatable-alerts-timeline')) {
            $pending->each(fn (array $history) => $this->histories()->create($history));
        }
    }

    public function recordHistory(string $event, Collection $old, Collection $new, Collection $pending): void
    {
        if ($new->isEmpty()) {
            return;
        }

        $pending->push([
            'event' => $event,
            'old' => $old,
            'new' => $new,
        ]);
    }
}
