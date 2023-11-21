<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
