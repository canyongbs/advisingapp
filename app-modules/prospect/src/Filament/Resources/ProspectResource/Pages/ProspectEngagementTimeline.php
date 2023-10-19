<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Filament\Pages\Timeline;
use Assist\Timeline\Actions\SyncTimelineData;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Models\Timeline as TimelineModel;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ProspectEngagementTimeline extends Timeline
{
    protected static string $resource = ProspectResource::class;

    protected static ?string $navigationLabel = 'Engagement Timeline';

    public string $emptyStateMessage = 'There are no engagements to show for this prospect.';

    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public function mount($record): void
    {
        $this->recordModel = $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

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
