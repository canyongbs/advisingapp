<?php

namespace Assist\Timeline\Actions;

use Illuminate\Support\Carbon;
use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use Assist\Timeline\Exceptions\ModelMustHaveATimeline;
use Assist\Timeline\Models\Contracts\ProvidesATimeline;

class SyncTimelineData
{
    public function now(Model $recordModel, $modelsToTimeline): void
    {
        if (cache()->has("timeline.synced.{$recordModel->getMorphClass()}.{$recordModel->getKey()}")) {
            return;
        }

        $aggregateRecords = collect();

        foreach ($modelsToTimeline as $model) {
            if (! in_array(ProvidesATimeline::class, class_implements($model))) {
                throw new ModelMustHaveATimeline("Model {$model} must have a timeline available");
            }

            $aggregateRecords = $aggregateRecords->concat($model::getTimelineData($recordModel));
        }

        $aggregateRecords = $aggregateRecords->sortByDesc(function ($record) {
            return Carbon::parse($record->timeline()->sortableBy())->timestamp;
        });

        $aggregateRecords->each(function ($record) use ($recordModel) {
            $timelineRecord = Timeline::firstOrCreate([
                'entity_type' => $recordModel->getMorphClass(),
                'entity_id' => $recordModel->getKey(),
                'timelineable_type' => $record->getMorphClass(),
                'timelineable_id' => $record->getKey(),
                'record_sortable_date' => $record->timeline()->sortableBy(),
            ]);

            if (! $timelineRecord->wasRecentlyCreated) {
                $timelineRecord->touch();
            }
        });

        cache()->put(
            "timeline.synced.{$recordModel->getMorphClass()}.{$recordModel->getKey()}",
            now(),
            now()->addMinutes(60)
        );
    }
}
