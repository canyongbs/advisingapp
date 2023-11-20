<?php

namespace Assist\Timeline\Actions;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Assist\Timeline\Exceptions\ModelMustHaveATimeline;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;

class AggregatesTimelineRecordsForModel
{
    public Collection $aggregateRecords;

    public function handle(Model $record, array $modelsToTimeline): Collection
    {
        $aggregateRecords = collect();

        foreach ($modelsToTimeline as $model) {
            if (! in_array(ProvidesATimeline::class, class_implements($model))) {
                throw new ModelMustHaveATimeline("Model {$model} must have a timeline available");
            }

            $aggregateRecords = $aggregateRecords->concat($model::getTimelineData($record));
        }

        return $aggregateRecords->sortByDesc(function ($record) {
            return Carbon::parse($record->timeline()->sortableBy())->timestamp;
        });
    }
}
