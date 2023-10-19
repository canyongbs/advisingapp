<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Filament\Pages\Timeline;
use Assist\Timeline\Actions\SyncTimelineData;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Models\Timeline as TimelineModel;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class StudentEngagementTimeline extends Timeline
{
    protected static string $resource = StudentResource::class;

    protected static ?string $navigationLabel = 'Engagement Timeline';

    public string $emptyStateMessage = 'There are no engagements to show for this student.';

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public function mount($record): void
    {
        $this->recordModel = $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        // TODO We need to determine a way to figure out if we NEED to sync the timeline data
        // we can probably do this in a couple of ways
        // 1. Utilizing the updated at of the timeline record
        // 2. Grabbing the last timelineable model and checking if it's in the timeline
        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);
    }

    protected function getViewData(): array
    {
        $timelineRecords = TimelineModel::query()
            ->forEducatable($this->recordModel)
            ->whereIn(
                'timelineable_type',
                collect($this->modelsToTimeline)->map(fn ($model) => resolve($model)->getMorphClass())->toArray()
            )
            ->orderBy('record_creation', 'desc')
            ->simplePaginate($this->recordsPerPage);

        return [
            'timelineRecords' => $timelineRecords,
        ];
    }
}
